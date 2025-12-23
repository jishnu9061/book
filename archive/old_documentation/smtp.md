# SMTP

### Gmail SMTP Setup <a href="#smtp_settings" id="smtp_settings"></a>

To setup you Gmail account to to the App you need to setup 2 step authentication first. In that case please follow the following steps.

* Go to security(<https://myaccount.google.com/security>) page
* Navigate to the 2-Step Verification Option.&#x20;
* Setup the 2-Step Verification features if you didn't enabled yet.
* After complete 2-Step Verification setup scroll to the bottom of the page and you will an option called "App Passwords"
* You need to click on that(App password) option and setup App name and Password.
* Use the newly created App password as your SMTP account password.

The Gmail SMTP configuration would be look like as following.

<figure><img src="https://3304868254-files.gitbook.io/~/files/v0/b/gitbook-x-prod.appspot.com/o/spaces%2F7fqmXAeiEs06SyjI9x4p%2Fuploads%2FvI6rni2K5olLRWgQfJ15%2FScreenshot%202024-01-18%20at%209.17.50%20AM.png?alt=media&#x26;token=be148197-b40d-4113-8455-cf88dc72cb02" alt=""><figcaption><p>Gmail SMTP</p></figcaption></figure>

If you setup \`.env\` file it will be look like as following for the Gmail SMTP

```
MAIL_MAILER=smtp
MAIL_HOST="smtp.gmail.com"
MAIL_PORT=465
MAIL_USERNAME="your-name@gmail.com"
MAIL_PASSWORD="askd alsk asdd poiu"
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="your-name@gmail.com"
MAIL_FROM_NAME=HelpDesk
```

A tutorial video about Gmail SMTP setup

{% embed url="<https://www.youtube.com/watch?v=jFRZrol56SM>" %}
Gmail SMTP Setup
{% endembed %}

### Hosting SMTP Settings <a href="#smtp_settings" id="smtp_settings"></a>

You can use any SMTP server like your hosting email’s info, MailGun SMTP info, etc. The following is an example link about how to get your hosting email account configuration.

#### How to Get Email Account Configuration Details for cPanel Email

Gathering information to use your cPanel email on clients such as Outlook, Thunderbird, etc.

To locate the IMAP/POP and SMTP configuration details of your email accounts, log into your cPanel, head to the Email section and select Email Accounts:

<figure><img src="https://3304868254-files.gitbook.io/~/files/v0/b/gitbook-x-prod.appspot.com/o/spaces%2F7fqmXAeiEs06SyjI9x4p%2Fuploads%2FFEWNZGCUnBB8YubN1pIl%2FScreenshot%202024-01-18%20at%209.24.28%20AM.png?alt=media&#x26;token=9b6f04b8-1e05-4365-b7c1-2d95ec9c4e84" alt=""><figcaption><p>Email Accounts</p></figcaption></figure>

Once there, locate the desired email account and click on Connect Devices:

<figure><img src="https://3304868254-files.gitbook.io/~/files/v0/b/gitbook-x-prod.appspot.com/o/spaces%2F7fqmXAeiEs06SyjI9x4p%2Fuploads%2Fnry382YHkIirhVU7acSu%2FScreenshot%202024-01-18%20at%209.25.56%20AM.png?alt=media&#x26;token=869a204f-861e-48ec-b407-5abf104d5d38" alt=""><figcaption><p>Connect Devices</p></figcaption></figure>

You will find the IMAP/POP and SMTP Configuration under Mail Client Manual Settings:

<figure><img src="https://3304868254-files.gitbook.io/~/files/v0/b/gitbook-x-prod.appspot.com/o/spaces%2F7fqmXAeiEs06SyjI9x4p%2Fuploads%2FPl6H9eYAC9QRESweWoWv%2FScreenshot%202024-01-18%20at%209.26.49%20AM.png?alt=media&#x26;token=51a8ac5f-e14e-4941-918a-d5ee4edfe26e" alt=""><figcaption><p>SMTP Configuration</p></figcaption></figure>

#### Please use `tls` for the `465` port as your Mail Encryption option.

Always use the Secure SSL/TLS settings 💡

If you use any VPS or other panel system instead of cPanel or you can't find your site's internal email configuration just ask your hosting provider regarding that. They will help you to find out your email SMTP configurations.
