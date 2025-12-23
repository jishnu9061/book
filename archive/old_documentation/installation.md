# Installation

Welcome to the HelpDesk installation page. It's much easier to install HelpDesk on your web server. It's not required to have the technical skill to install HelpDesk. You need to follow a few steps to install it on your server. You can choose any shared hosting, including cPanel or VPS server, which supports PHP and MySQL.

### Server Requirements <a href="#server-requirements" id="server-requirements"></a>

Before installing HelpDesk, ensure your server meets the following requirements. PHP and MySQL have pre-installed all of the shared hosting providers by default.

* PHP 8.1+
* MySQL 5.7+

You check the PHP version if you write `phpinfo();` on a PHP file. Also, you can ask your hosting provider if they support PHP 8.1+ or MySQL 5.7+.

You will also see a list with success and error icons on the installation screen about your server configuration.

### Domain or Subdomain

You must need to choose a domain or subdomain to install on the HelpDesk. If your server use cPanel or similar tools you will find domain or sub-domain options on that panel. The following is an example tutorial to create a subdomain from the cPanel.

{% embed url="<https://www.youtube.com/watch?v=mpzLLBQtDcM>" %}
Create a sub-domain from cPanel.
{% endembed %}

### Upload Files <a href="#upload" id="upload"></a>

\
Extract the downloaded archive package. You will see the following files after downloading the zip.

* **HelpDesk** (folder) - this folder includes the main(script) files.
* **Documentation** (folder) - this folder included documentation(that will rediect the documentation web link)
* **readMe.txt** (file) - here defined initial instructions regarding the script

Now, you must upload all files inside the **HelpDesk** folder to your website's **`root`** directory. You can use an FTP client or File Manager(cPanel/file management tool) to upload files.

### Create Database

Create a database for HelpDesk through your server control panel. If your server has phpMyAdmin, you can create a database using phpMyAdmin.

\*\*\* You can follow the following tutorial to create a new database in cPanel\\

{% embed url="<https://support.hostinger.com/en/articles/4548533-how-to-create-a-new-mysql-database-in-cpanel>" %}
Create a new MySQL database in cPanel
{% endembed %}

Also, the following one is an example video tutorial to create a new database in cPanel

{% embed url="<https://www.youtube.com/watch?v=aano0q06v5A>" %}
Create a Database on cPanel - HelpDesk
{% endembed %}

### Install

Now, go to your website address and you will see an installation wizard. You need to follow that wizard to complete the installation. Following are the steps you need to follow to complete the installation.

### Installation Initialization <a href="#installation-steps" id="installation-steps"></a>

You will be asked for the following information while proceeding with the installation.

* CodeCanyon purchase code
* Database name, hostname, username, password

You will see the following screen if you visit your website address. An example website URL as follow `website.com`

<figure><img src="https://3304868254-files.gitbook.io/~/files/v0/b/gitbook-x-prod.appspot.com/o/spaces%2F7fqmXAeiEs06SyjI9x4p%2Fuploads%2FTu7ijZfxM6xgfuG8D7dY%2F1_installer.png?alt=media&#x26;token=a2cc40e6-2e17-4d56-ac0b-1cb9bf2d7d0c" alt=""><figcaption><p>HelpDesk Installer</p></figcaption></figure>

\*\* Click on the “**Check Requirements**” button for the next steps.

### Checking Server Requirements <a href="#pre-installation" id="pre-installation"></a>

Server requirements page checks if your server meets the requirements including PHP modules such as Openssl, Pdo, Mbstring, Tokenizer, Xml, Ctype, JSON, Curl, etc.

In this step, we ran the diagnosis on your server. Please review the items that have a red mark on them. If everything is green, you are good to go to the next step.

<figure><img src="https://3304868254-files.gitbook.io/~/files/v0/b/gitbook-x-prod.appspot.com/o/spaces%2F7fqmXAeiEs06SyjI9x4p%2Fuploads%2FAYaEfNt3ITbJPrXFgqM2%2F2_requirements.png?alt=media&#x26;token=45476643-ee4c-46a7-994f-cd13744fc202" alt=""><figcaption><p>Server Requirements</p></figcaption></figure>

\*\* Click on the “**Check Permissions**” button

### Files and Folder Permissions

Files and Folder Permissions page with check if has the correct directory permissions to set up ProSchedule.

Directories within the `storage` , `bootstrap/cache` , `resources/lang` and the `public` directories should be writable by your web server.

<figure><img src="https://3304868254-files.gitbook.io/~/files/v0/b/gitbook-x-prod.appspot.com/o/spaces%2F7fqmXAeiEs06SyjI9x4p%2Fuploads%2FDwSSyk0taNw1XSfsa0Rp%2F3_permissions.png?alt=media&#x26;token=c50b73fb-72c7-46ca-a437-ee3a3770d833" alt=""><figcaption><p>Permissions</p></figcaption></figure>

If you see anything with red marked(not ticked), you may need to correct permissions in that case for the specific Item.

\*\* Click on the “**Next**” button

### Purchase Code

Provide your CodeCanyon purchase code. You will find your purchase code where you downloaded the script file. You can go to this link also to know your purchase code.

{% embed url="<https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code>" %}
Purchase Code finding help link
{% endembed %}

<figure><img src="https://3304868254-files.gitbook.io/~/files/v0/b/gitbook-x-prod.appspot.com/o/spaces%2F7fqmXAeiEs06SyjI9x4p%2Fuploads%2FkNShrusZIP5gIsqAVCYk%2F4_purchase_code.png?alt=media&#x26;token=5639fe75-bac6-48c6-9e73-92b60b2e7317" alt=""><figcaption><p>Purchase Code</p></figcaption></figure>

\*\* Click on the “**Submit**” button after typing/paste your purchase code

### Database setup

Fill out this form with your database connection details. If you haven't created a database yet, please create a new one and fill out the following form.

Create a database for ProSchedule through your server control panel. If your server has phpMyAdmin, you can create a database using phpMyAdmin.

\* cPanel – you can follow the following tutorial to create a new database in cPanel\\

{% embed url="<https://support.hostinger.com/en/articles/4548533-how-to-create-a-new-mysql-database-in-cpanel>" %}
Create a MySQL database in cPanel
{% endembed %}

<figure><img src="https://3304868254-files.gitbook.io/~/files/v0/b/gitbook-x-prod.appspot.com/o/spaces%2F7fqmXAeiEs06SyjI9x4p%2Fuploads%2FMJ43NRDhFqFkziX1ygg3%2F5_database_information.png?alt=media&#x26;token=245a8337-d715-4c14-8d87-4c51720d8d04" alt=""><figcaption><p>Application and Database Information update screenshot</p></figcaption></figure>

\*\* Click on the “**Submit**” button

If you submit the correct database you will redirect to the login credentials setup page to set up your login credentials.

### Admin Login Credentials <a href="#login-credentials" id="login-credentials"></a>

In this step, you will need to set up your admin credentials.

<figure><img src="https://3304868254-files.gitbook.io/~/files/v0/b/gitbook-x-prod.appspot.com/o/spaces%2F7fqmXAeiEs06SyjI9x4p%2Fuploads%2FkQCVKPh88M02ngO6bKyz%2F6_admin_credentails.png?alt=media&#x26;token=d8dab1ac-374f-4346-abc8-20d78aa9c3cc" alt=""><figcaption><p>Admin User Credentials</p></figcaption></figure>

\*\* Click on the “**Complete Setup**” button with filling up the input fields

### Complete Setup <a href="#complete-setup" id="complete-setup"></a>

After completing the above steps you will see an installation finished screen as like bellow.

<figure><img src="https://3304868254-files.gitbook.io/~/files/v0/b/gitbook-x-prod.appspot.com/o/spaces%2F7fqmXAeiEs06SyjI9x4p%2Fuploads%2F241EVDDm3NxPLVsxHLWW%2F7_finished_installation.png?alt=media&#x26;token=c7a7fabe-66cb-4f84-a152-82d7576b95f8" alt=""><figcaption><p>Installation finished screen.</p></figcaption></figure>

**\*\* Your application is ready to use. You can visit now on HomePage or Dashboard.**

### Video Tutorial <a href="#video-tutorial" id="video-tutorial"></a>

You may watch the following tutorial to know the complete process of the HelpDesk script installation.

{% embed url="<https://www.youtube.com/watch?v=QwTvJfH7bak>" %}

The above video is a complete tutorial about Installing HelpDesk on the cPanel or shared hosting.

\\
