# CRM App

Customer relationship management application created for learning purposes.

## Stack

* PHP 8.0
* Symfony 5.3
* MariaDB 5.6

## Description

I have made my best to ensure that the application is extensive and at least a bit close to the "real world" product, so we have here many types of entities - contacts, companies, notes, as well as deals and meetings. There is also a dashboard with a bunch of charts, so users can see the last activity of their team, analyze companies in terms of belonging to the industry, etc. 

Users are able to register and cooperate in their workspace. There are three different user roles: admin, manager and user, and each of them have different permissions.

## What I have learned
* Building complex projects with Symfony
* Doctrine relations
* Creating Symfony forms, reusable types, custom validators, etc.
* Managing user roles and authorization
* Testing Symfony applications

## How to see it online
[Live demo](http://crm.testingwebsite.pl)

* You can create your workspace by filling the registration form.
* You can also log in as a guest user to the existing workspace with a lot of dummy data.

To log in as a guest user, fill the login form with the following info:

**login:** testuser@email.com

**password**: 12341234

Notice that as a guest user you will be able to see almost all data (related to this workspace), but not to edit, delete or create new data.