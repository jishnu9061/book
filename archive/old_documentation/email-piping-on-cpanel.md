# Email Piping on cPanel

## 📬 HelpDesk Email Piping Setup Guide (for cPanel Hosting)

This guide will help you configure **email piping** on your cPanel-based server to connect with the HelpDesk system via a secure API webhook.

***

### ✅ Requirements

Before setting up, make sure:

* Your HelpDesk system is deployed and accessible online.
* The API endpoint `/api/email-handler` is working.
* Your `.env` file includes a secure token:

```
EMAIL_WEBHOOK_TOKEN=your-secure-token
```

***

### 🛠️ Step 1: Create the Pipe Script

1. Create a directory in your cPanel file system (outside `public_html` recommended), e.g. `email_pipe`.
2. Inside that directory, create a file named `pipe.php`.
3. Paste the following content inside `pipe.php`:

```php
#!/usr/bin/php -q
<?php

$app_uri = "https://yoursite.com/api/email-handler";
$pipe_token = 'YOUR_PIPE_TOKEN';

$stdin = fopen("php://stdin", "r");
$email = '';
while (!feof($stdin)) {
    $email .= fread($stdin, 1024);
}
fclose($stdin);


preg_match('/^From:\s*(.+)/mi', $email, $fromMatch);
$fromLine = trim($fromMatch[1] ?? '');
$from_email = $fromLine;
$from_name = '';

if (preg_match('/^(.*)<(.+@.+)>$/', $fromLine, $matches)) {
    $from_name = trim($matches[1], '" ');
    $from_email = trim($matches[2]);
}

// Optional: save for debugging
// file_put_contents("/home/yourcpaneluser/email_pipe/debug_" . time() . ".eml", $email);

preg_match('/^Subject:\s*(.+)/mi', $email, $subjectMatch);
preg_match('/^Message-ID:\s*(.+)/mi', $email, $messageIdMatch);
preg_match('/^In-Reply-To:\s*(.+)/mi', $email, $inReplyToMatch);

$body = preg_split("/\r?\n\r?\n/", $email, 2)[1] ?? '';

$payload = [
    'from'         => $from_email,
    'from_name'    => $from_name,
    'subject'      => trim($subjectMatch[1] ?? '(no subject)'),
    'body'         => trim($body),
    'message_id'   => trim($messageIdMatch[1] ?? ''),
    'in_reply_to'  => trim($inReplyToMatch[1] ?? ''),
    'date'         => date('c'),
];

// ✅ 3. Send to Laravel webhook
$ch = curl_init($app_uri);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-Webhook-Token: ' . $pipe_token
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
curl_close($ch);
```

> 🔐 Replace `YOUR_TOKEN_HERE` with your actual token from `.env`

***

### 🔧 Step 2: Set Permissions

Make the script executable:

```bash
chmod 755 pipe.php
```

#### ✅ Or Set File Permission to 755 in cPanel File Manager

1. Go to File Manager.
2. Navigate to the folder `email_pipe`&#x20;
3. Right-click on pipe.php and select “Change Permissions”.
4. In the permission dialog:
   * ✅ Check: Read, Write, and Execute for Owner
   * ✅ Check: Read and Execute for Group
   * ✅ Check: Read and Execute for World
5. The permission value should now show as 755.
6. Click Save.

***

### 💌 Step 3: Add Email Forwarder in cPanel

1. Log into your cPanel account.
2. Navigate to **Email > Forwarders**.
3. Click **“Add Forwarder”**.
4. Under *Address to Forward*, enter your email (e.g., `support@yourdomain.com`).
5. Under *Destination > Advanced Options*, select **“Pipe to a Program”**.
6. Enter the relative path to the script:

```
email_pipe/pipe.php
```

> ⚠️ Do **not** use a leading `/` — the path is relative to your home directory.

***

### ✅ You’re Done!

Now, any email sent to your forwarded address will be piped to your HelpDesk’s API, where it will automatically create or reply to a ticket.

If you need help or run into issues, please contact our support team.
