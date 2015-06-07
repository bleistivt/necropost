<?php

$PluginInfo['necropost'] = array(
    'Name' => 'Necropost Warning',
    'Description' => 'Warn users when they are about to comment on an old discussion.',
    'Version' => '0.1',
    'MobileFriendly' => true,
    'SettingsUrl' => 'settings/necropost',
    'SettingsPermission' => 'Garden.Settings.Manage',
    'Author' => 'Bleistivt',
    'AuthorUrl' => 'http://bleistivt.net',
    'License' => 'GNU GPL2'
);

class NecropostPlugin extends Gdn_Plugin {

    public function settingsController_necropost_create($sender) {
        $sender->permission('Garden.Settings.Manage');
        $sender->addSideMenu('settings/necropost');
        $sender->setData('Title', T('Necropost Warning'));

        $conf = new ConfigurationModule($sender);
        $conf->initialize(array(
            'Necropost.Days' => array(
                'Control' => 'textbox',
                'LabelCode' => 'Minimum age of a discussion (days)',
                'Default' => 356,
                'Options' => array('maxlength' => 5)
            ),
            'Necropost.Message' => array(
                'Control' => 'textbox',
                'LabelCode' => 'Message',
                'Default' => $this->message(),
                'Options' => array('MultiLine' => true)
            )
        ));
        $conf->renderAll();
    }

    public function discussionController_render_before($sender) {
        if (!$sender->data('Discussion') || time() - strtotime($sender->data('Discussion')->DateLastComment) < c('Necropost.Days', 365) * 86400) {
            return;
        }
        $sender->Head->addString(
            '<script type="text/javascript">
                jQuery(function($){
                    $("#Form_Comment").one("focus", ".TextBox", function() {
                        gdn.informMessage("'.$this->message().'", "Dismissable");
                    });
                });
            </script>'
        );
    }

    private function message() {
        return t(c('Necropost.Message', 'This discussion has been inactive for more than a year. Please comment only if you have something constructive to add.'));
    }

}
