# Glibix Hidemail - Avoid being indexed.

An open source project that enables users to hide their email address by posting a link instead of posting the actual email so that only humans can recover the actual email ID.

## The Problem

Many people submit their email ID in various websites where it becomes publically accessible - which means that even bots / crawlers harvesting email address from the Internet gets hold of your email ID. And before you know it, you are targetted with spam and phishing mails!

## The Solution

A way to publish your email address to the people looking for it, but at the same time ensure that only humans get it.

This soluction already existed in reCAPTCHA v1.0, but when v1.0 was shutdown, the hidemail feature was removed completely.

## Glibix Hidemail

This applicaiton gives you a similar way to protect your email addresses. You can submit your email address at https://hidemail.glibix.com and you will be given a link (_reveal link_) & a masked version of your email address. After that you can publish this masked email with a hyperlink to the _reveal link_. Since the full email isn't published, bots / crawlers wont be able to recover the email. Even if a bot manages to follow the link, it will have to fight the power of reCAPTCHA to get the email address!

## Features

We can categorize users to 3 types, based on their usage:
1. The layman / blogger - _needs to hide only 1 or 2 email addresses_.
1. The webmaster / admin - _needs to hide email addresses submitted to be publically available (like in the body of a forum post)_.
1. THe product developer - _needs a way to hide email addresses for it's customers using the customer's account_.

And that's why we have the bekiw features.

### AES 256 Enterprise Grade Encryption

All email IDs being hidden are stored with enterprise grade AES 256 encryption. This makes sure that even if a hacker gains access to our database, retrieving the email IDs would be near to impossible. Same is the case with your login email.

### API Access

We support OAuth 2.0. With API support, you can hide email IDs, search hidden email IDs and delete them programatically. You can even create a personal access token if you do not wish to use a complete OAuth 2.0 authentication.

### Power of reCAPTCHA

reCAPTCHA is a free service from Google that protects your website from spam and abuse. All reveal requests are validated by reCAPTCHA before the email ID is displayed on screen.

### OpenSource

So anyone can come and review the code.

### Social Login

Integrated social login so that you can register or login with a click - using your Facebook or Google account!
