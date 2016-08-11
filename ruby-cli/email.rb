require "pony"

puts "run!!"
#Pony.mail(
#   :to => 'socendani@gmail.com',
#   :from => 'socendani@gmail.com',
#   :subject => 'hi',
#   :body => 'Hello there.')
#texto=File.open("email.rb","r").readlines.to_s
#texto.gsub(/smtp.datagrama.net/,"**")
texto="Enviando email desde script escrito en Ruby on Rails. Dani Morte."
#puts texto.readlines()

Pony.mail(
    :to => 'daniel.morte@unit4.com',
   :from => '****',
   :subject => 'prova de email..using smtp gmail',
   :body => "Prova de email desde :\n RUBY + VIM (usando la gema pony -  ver en github) <br> #{texto}",
    :via => :smtp,
    :smtp => {


     :tls    => true,
     :host   => 'smtp.gmail.com',
     :port   => '587',
     :user   => '****S@gmail.com',
     :password   => '***',

     :auth   => :login, # :plain, :login, :cram_md5, no auth by default
     :domain => "localhost.localdomain" # the HELO domain provided by the client to the server
     }
   )
puts "fin"
#email = RubySMTP.new()
#email.smtp_host="smt.datagrama.net"
#email.from = 'socendani@gmail.com'
#email.to = 'socendani@gmail.com'
#email.subject = 'test subject'
#email.message = 'hi'
#email.send
