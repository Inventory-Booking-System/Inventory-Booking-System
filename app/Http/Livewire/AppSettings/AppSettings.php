<?php

namespace App\Http\Livewire\AppSettings;

use Livewire\Component;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;

class AppSettings extends Component
{
    //Mail Settings
    public $mail;
    public $notification;

    public function rules()
    {
        return [
            'mail' => 'array',
            'mail.mailer' => 'required|string|in:smtp,ses,mailgun,postmark,sendmail',
            'mail.host' => 'required',
            'mail.port' => 'required|integer',
            'mail.username' => 'string',
            'mail.password' => 'string',
            'mail.encryption' => 'string|in:ssl,tls,starttls,null',
            'mail.from_address' => 'required|email',
            'mail.cc_address' => 'email',
            'notification' => 'array',
            'notification.overdue_emails' => 'boolean',
            'notification.setup_emails' => 'boolean',
        ];
    }

    public function save()
    {
        $this->validate();

        //Mail Settings
        DotenvEditor::setKey('MAIL_MAILER', $this->mail['mailer']);
        DotenvEditor::setKey('MAIL_HOST', $this->mail['host']);
        DotenvEditor::setKey('MAIL_PORT', $this->mail['port']);
        DotenvEditor::setKey('MAIL_USERNAME', $this->mail['username']);
        DotenvEditor::setKey('MAIL_PASSWORD', $this->mail['password']);
        DotenvEditor::setKey('MAIL_ENCRYPTION', $this->mail['encryption']);
        DotenvEditor::setKey('MAIL_FROM_ADDRESS', $this->mail['from_address']);
        DotenvEditor::setKey('MAIL_CC_ADDRESS', $this->mail['cc_address']);

        //Notifications Settings
        DotenvEditor::setKey('NOTIFICATION_OVERDUE_EMAILS', $this->notification['overdue_emails']);
        DotenvEditor::setKey('NOTIFICATION_SETUP_EMAILS', $this->notification['setup_emails']);

        DotenvEditor::save();
    }

    public function mount()
    {
        //Mail Settings
        $this->mail['mailer'] = DotenvEditor::getValue('MAIL_MAILER');
        $this->mail['host'] = DotenvEditor::getValue('MAIL_HOST');
        $this->mail['port'] = DotenvEditor::getValue('MAIL_PORT');
        $this->mail['username'] = DotenvEditor::getValue('MAIL_USERNAME');
        $this->mail['password'] = DotenvEditor::getValue('MAIL_PASSWORD');
        $this->mail['encryption'] = DotenvEditor::getValue('MAIL_ENCRYPTION');
        $this->mail['from_address'] = DotenvEditor::getValue('MAIL_FROM_ADDRESS');
        $this->mail['cc_address'] = DotenvEditor::getValue('MAIL_CC_ADDRESS');

        //Notifications Settings
        $this->notification['overdue_emails'] = DotenvEditor::getValue('NOTIFICATION_OVERDUE_EMAILS');
        $this->notification['setup_emails'] = DotenvEditor::getValue('NOTIFICATION_SETUP_EMAILS'); 
    }

    public function render()
    {
        return view('livewire.app-settings.app-settings');
    }
}
