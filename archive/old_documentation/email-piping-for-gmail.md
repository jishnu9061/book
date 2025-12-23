# Email Piping for Gmail

## 📬 HelpDesk Email Piping Setup Guide (for Gmail via Google Apps Script)

This guide will help you configure **Gmail-based email piping** using a Google Apps Script. The script will forward unread emails from your inbox to your HelpDesk system's webhook, where tickets can be automatically created or updated.

***

### ✅ Requirements

Before setting up, make sure:

* Your HelpDesk system is live and has a working API endpoint at `/api/email-handler`.
* Your `.env` file includes a secure token for email piping:

```
EMAIL_WEBHOOK_TOKEN=your-secure-token
```

***

### 🛠️ Step 1: Create the Google Apps Script

1. Go to [https://script.google.com](https://script.google.com/) and create a new project.
2. Replace the default code with the following script:
3. After doing that set `app_uri` and `pipe_token`&#x20;
4. Click Save

```javascript
function forwardGmailToWebhook() {
  var app_uri = "https://website.com/api/email-handler";
  var pipe_token = 'YOUR_TOKEN_HERE';
  try {
    var threads = GmailApp.search("is:unread label:inbox");

    for (var i = 0; i < threads.length; i++) {
      var messages = threads[i].getMessages();

      for (var j = 0; j < messages.length; j++) {
        var msg = messages[j];

        // Parse From
        var fromRaw = msg.getFrom().trim();
        var emailMatch = fromRaw.match(/<([^>]+)>/);
        var from_email = emailMatch ? emailMatch[1] : fromRaw;

        var nameMatch = fromRaw.match(/^(.*?)\s*</);
        var from_name = nameMatch ? nameMatch[1].trim().replace(/^"|"$/g, '') : '';

        // Parse To
        var toRaw = msg.getTo().trim();
        var toMatch = toRaw.match(/<([^>]+)>/);
        var to = toMatch ? toMatch[1] : toRaw;

        // Extract headers manually from raw email
        var raw = msg.getRawContent();
        var message_id = extractHeader(raw, 'Message-ID');
        var in_reply_to = extractHeader(raw, 'In-Reply-To');
        var references = extractHeader(raw, 'References');

        // Detect reply
        var subject = msg.getSubject() || '';
        var body = msg.getPlainBody() || '';
        var is_reply =
          subject.toLowerCase().startsWith("re:") ||
          in_reply_to !== "" ||
          raw.includes("In-Reply-To:") ||
          raw.includes("References:") ||
          (body.includes("On ") && body.includes("wrote:"));

        var payload = {
          from_name: from_name,
          from: from_email,
          to: to,
          subject: subject,
          body: body,
          date: msg.getDate() ? msg.getDate().toISOString() : new Date().toISOString(),
          message_id: message_id,
          in_reply_to: in_reply_to,
          references: references,
          is_reply: is_reply
        };
        Logger.log(JSON.stringify(payload, null, 2));

        // Send to webhook
        UrlFetchApp.fetch(app_uri, {
          method: "post",
          contentType: "application/json",
          payload: JSON.stringify(payload),
          headers: {
            'X-Webhook-Token': pipe_token
          },
          muteHttpExceptions: true
        });
      }

      threads[i].markRead();
    }

  } catch (error) {
    Logger.log("Error forwarding Gmail to webhook: " + error.message);
  }
}

// 📦 Utility: extract header from raw content
function extractHeader(raw, headerName) {
  var regex = new RegExp("^" + headerName + ":(.*)$", "mi");
  var match = raw.match(regex);
  return match ? match[1].trim() : "";
}
```

> 🔐 Replace `YOUR_TOKEN_HERE` with your actual token from `.env`&#x20;

***

### 🔄 Step 2: Authorize & Run the Script

1. Click the ▶️ (Run) button next to the `forwardGmailToWebhook` function.
2. Authorize the script to access your Gmail account.
3. Open **Triggers** in the left sidebar > click **Add Trigger**.
4. Select:
   * Function: `forwardGmailToWebhook`
   * Event source: **Time-driven**
   * Type: Every 5 minutes (or your preferred interval)

***

### ✅ You’re Done!

Now, unread emails in your Gmail inbox (with `inbox` label) will be automatically sent to your HelpDesk system and processed as tickets.

If you need help, feel free to contact our support team.
