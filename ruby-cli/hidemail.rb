#!/usr/bin/env ruby

#email = STDIN.read
email=ARGV[0]
url_email = email.gsub(/./) { |c| '%' + c.unpack('H2' * c.size).join('%').upcase }
html_email = url_email[1..-1].split(/%/).collect { |c| sprintf("&#%03d;", c.to_i(16)) }.join

print "<a href=\"mailto:#{url_email}\">#{html_email}</a>"
