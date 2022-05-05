<?php
/**
 *
 * class FfuenfCommon
 *
 * @category   Shopware
 * @package    Shopware\Plugins\FfuenfCommon
 * @author     Achim Rosenhagen / ffuenf - Pra & Rosenhagen GbR
 * @copyright  Copyright (c) 2022, Achim Rosenhagen / ffuenf - Pra & Rosenhagen GbR (https://www.ffuenf.de)
 *
 */

namespace FfuenfCommon\Bootstrap;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Shopware\Models\Mail\Mail;

class Installer
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var ContainerBuilder
     */
    private $container;

    public function __construct($path, $container)
    {
        $this->path       = $path;
        $this->container  = $container;
    }

    public function install()
    {
        $sql = file_get_contents($this->path . '/Resources/sql/install.sql');
        $this->container->get('shopware.db')->query($sql);
        $mail = $this->container->get('models')->getRepository(Mail::class)->findOneBy(['name' => 'sCRONWARNING']);
        if ($mail instanceof Mail) {
            return $mail;
        }
        $mail = new Mail();
        $mail->setDirty(true);
        $mail->setName('sCRONWARNING');
        $mail->setFromMail('{config name=mail}');
        $mail->setFromName('{config name=shopName}');
        $mail->setSubject('ATTENTION: inactive cronjobs');
        $mail->setMailtype(Mail::MAILTYPE_SYSTEM);
        $mail->setIsHtml(true);
        $mail->setContent('Cron Monitoring

ATTENTION - there are inactive cronjobs:
{foreach item=cron from=$downCrons}
- {$cron.name} ({$cron.action}) - last run: {$cron.end}
{/foreach}');
        $mail->setContentHtml('<b>Cron Monitoring</b>
<br /><br />ATTENTION - there are inactive cronjobs:<br /><br />
<ul>{foreach item=cron from=$downCrons}<li>{$cron.name} ({$cron.action}) - last run: {$cron.end}</li>{/foreach}</ul>');
        try {
            $this->container->get('models')->persist($mail);
            $this->container->get('models')->flush();
        } catch (Exception $e) {
            throw new Enlight_Exception("E-Mail-Template sCRONWARNING couldn't be created!");
        }
    }

    public function update200()
    {
        $sql = file_get_contents($this->path . '/Resources/sql/update200.sql');
        $this->container->get('shopware.db')->query($sql);
    }
}
