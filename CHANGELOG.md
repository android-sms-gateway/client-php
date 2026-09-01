# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### New Features

#### Outgoing MMS

- **Outgoing MMS support** — pass an `MmsMessage` (instead of a text string) to `Message` or `MessageBuilder` to send an MMS with an optional subject, body, and attachments:
  ```php
  $mms = new MmsMessage('Subject', 'Body', [
      new MmsAttachment('image/png', base64_encode($bytes), 'photo.png'),
  ]);
  $message = (new MessageBuilder($mms, ['+15550100']))->build();
  ```
  Attachments carry a MIME type, base64-encoded data, and an optional filename; null subject, text, and filename are omitted from the request body. MMS fields are encrypted when an `Encryptor` is set, and an MMS message is mutually exclusive with an SMS text body on the wire.

## [2.6.1] - 2026-09-23

### Bug Fixes

- **Message priority always serialized** — a message's `priority` is now always included in the JSON body (defaulting to `0` when unset), matching the wire format of the other official SDKs.

## [2.6.0] - 2026-08-19

### New Features

#### Inbox refresh and batch webhooks

- **`RefreshInbox` with `InboxRefreshRequest`** — refresh the inbox and choose how webhook events are delivered with `WebhookDelivery` (`Disabled`, `Individual`, or `Batch`). `RequestInboxExport` is deprecated in favor of it.
- **Batch webhook events** — `sms:batch:received`, `sms:batch:data-received`, `mms:batch:received`, and `mms:batch:downloaded` events with typed payload classes (`SmsBatchReceivedPayload`, `SmsBatchDataReceivedPayload`, `MmsBatchReceivedPayload`, `MmsBatchDownloadedPayload`).