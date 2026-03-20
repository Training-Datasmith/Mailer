# Architecture: Mailer (Sylius Mailer Component)

## Purpose

Sylius Component that provides a transport-agnostic email sending API. It separates email template rendering from email delivery so each can be swapped independently.

## Directory Structure

```
Factory/      EmailFactory — creates Email model instances
Model/        Email value object and interface (code, enabled, from, subject, template)
Provider/     DefaultSettingsProvider (global from/name), EmailProvider (loads by code)
Renderer/
  Adapter/    Renderer adapters (Twig, default) implement AdapterInterface
Sender/
  Adapter/    Sender adapters implement AdapterInterface; CcAwareAdapterInterface for CC
  Sender.php  Orchestrator: renders then sends, dispatches events
Event/        EmailRenderEvent, EmailSendEvent for Symfony EventDispatcher hooks
spec/         PhpSpec behavioural specifications
```

## Key Design Decisions

- **Adapter pattern**: Both rendering and sending are swappable via constructor-injected adapters. Bundles (SyliusMailerBundle) wire the concrete adapters (Twig renderer, Symfony Mailer transport).
- **Email codes**: Emails are identified by string codes rather than class names, making them configurable in Yaml/XML without code changes.
- **CC awareness**: The `CcAwareAdapterInterface` extends the sender interface; the `Sender` checks for this interface at runtime to pass CC/BCC lists.
- **Event hooks**: Render and send events allow post-processing (e.g., attaching tracking pixels) without modifying the component.

## Extension Points

- Implement `AdapterInterface` in the Renderer namespace to add a custom template engine.
- Implement `AdapterInterface` in the Sender namespace to add a custom mailer transport.
- Subscribe to `EmailRenderEvent` and `EmailSendEvent` for cross-cutting concerns.

## Dependency Flow

```
Application code
  -> Sender::send(code, recipients, data)
    -> EmailProvider::getEmail(code)
    -> RendererAdapter::render(email, data)  -> RenderedEmail
    -> SenderAdapter::send(recipients, from, renderedEmail, ...)
```
