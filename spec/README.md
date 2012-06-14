RSPEC Testing
=============

Coding 101 says do not use Ruby to test PHP,  but I do not like CakePHP's test framework.  So I am going to use RSpec & Rest Client to take care of the testing.  Enjoy:)

Install Gems
-------------
Make sure to gem install each gem before testing.

`gem install rspec`

`gem install rest-client`
`gem install nokogiri`
`gem install activerecord`
`gem install mysql`

Set your vhost url in the spec_helper.rb file.
Setup your database settings in spec/config/database.yml

Usage
-----

In Terminal,  from the root directory of your app, type:

`rspec`