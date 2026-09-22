# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Bug Fixes

- **Message priority always serialized** — a message's `priority` is now always included in the JSON body (defaulting to `0` when unset), matching the wire format of the other official SDKs.

## [2.6.0] - 2026-08-19

### New Features

#### Inbox refresh and batch webhooks

- **`RefreshInbox` with `InboxRefreshRequest`** — refresh the inbox and choose how webhook events are delivered with `WebhookDelivery` (`Disabled`, `Individual`, or `Batch`). `RequestInboxExport` is deprecated in favor of it.
- **Batch webhook events** — `sms:batch:received`, `sms:batch:data-received`, `mms:batch:received`, and `mms:batch:downloaded` events with typed payload classes (`SmsBatchReceivedPayload`, `SmsBatchDataReceivedPayload`, `MmsBatchReceivedPayload`, `MmsBatchDownloadedPayload`).