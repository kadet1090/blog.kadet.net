---
tags: [almost-philosophy]
categories: [dsp, nucleus]
date: 2017-03-14
---
# Nucleus - what's that, and why do I even bother?

Well, maybe you know that [Nucleus] is my next (actually third or fourth) [XMPP] library for PHP. If you don't know what XMPP is, thats probably ok, because nowadays it isn't really popular, but once it was. That was long time ago, but nevertheless [XMPP] is quite cool protocol from programmers side and I just really like it. Why PHP? Oh well.

## Why PHP? But really WHY?

I know that PHP [is not](https://eev.ee/blog/2012/04/09/php-a-fractal-of-bad-design/) [the best](https://wiki.theory.org/YourLanguageSucks#PHP_sucks_because) language for that use case, but there are few reasons why I'm doing so. First of all, there are many libraries for other languages, which are quite good so I don't really see reason for doing another one. And as far as i can tell PHP has only 1 lib which is in fact in use - `xmpp.class.php` - and that's really old library, I'm not even sure if it is compatible with current XMPP standard, I really doubt it. There are also few abominations from my own creation - and i'm really terrified that someone actually uses it (or at least try to do so). None of those libraries could be described as modern, none of those should be used in production, and none of those is compatible with actual XMPPs RFC (They often base on old Jabber standard). And you may be surprised but there are in fact some apps that internally uses XMPP from PHP, for notifications etc. So it's not totally useless.

Also PHP is my main language (why? well, maybe i'll describe it sometime later), and I think that I know it pretty well so I won't be fighting with my lack of language knowledge but I can focus on architecture side, and just write (not so much) solid code. And architecture is language-agnostic so i'd be able to use everything that I learned in future projects. 

And last but not least, I just like using PHP that way - maybe it's some strange kind of Stockholm syndrome, but well, i don't care. And if you want to say that i'm dumb - fell free to do so, i'll fell free to ignore you. 

## Okay, so I told you about motivation, what about goals? 

Write solid, fully tested and modern PHP XMPP library, of course!

But to be precise, I want to fully implement XMPP RFCs: [RFC6120] - well, this one is actually implemented - and [RFC6121] \(implemented partially). Also, [Nucleus] is not aiming to be monolith library that does everything possible in XMPP, it'd be against XMPP philosophy. It's desired to allow programmers to easily implement what they really need and use only components which are required by their use case. Don't need to support MUC? No problem, just don't include MUC component and that's all. Actually the only thing that you cannot turn off is XMPP Stream support, even Authentication is done as separate component that could be removed and potentially substituted with some proprietary solution.

```php
$client = new \Kadet\Xmpp\XmppClient(new \Kadet\Xmpp\Jid('joe@example.com/somewhere'), [
    'loop'     => $loop,
    'password' => 'bruceschneiersepicpasspoem',
    'modules'  => [
        new \Kadet\Xmpp\Component\PingKeepAlive(), // Keep Alive ping
        new SubscriptionManager()                  // Managing subscriptions
    ] + default: [ 
        TlsEnabler::class    => new TlsEnabler(),        // Support for TLS
        Binding::class       => new Binding(),           // Resource Binding
        Authenticator::class => new SaslAuthenticator(), // User Authentication
        Roster::class        => new Roster()             // Roster management
    ]
]);
``` 

This also makes unit testing really easy because each component can be tested on it's own. I encourage you to visit [Trello] to see actual task list.

So, that's probably all for now.

[XMPP]: http://xmpp.org/
[nucleus]: https://github.com/kadet1090/nucleus
[RFC6120]: https://xmpp.org/rfcs/rfc6120.html
[RFC6121]: https://xmpp.org/rfcs/rfc6121.html
[Trello]: https://trello.com/b/WHQ6d3hw/nucleus