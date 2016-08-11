#@use: $ ruby dmr_join_mpg.rb directorio
$author="Dani Morte 2010"
$version="1.1"
$debug=true
$script=__FILE__
$output="dmr_output"
puts "-------------------------------------------"
puts "concatena videos *.avi y *.mpg manteniendo ratios"
puts "Author: "+$author
puts "Script: "+$script+" (#{$version}) "
puts "Dependences: mecoder (instalar MPlayer)"	
puts "Salida: "+$output
puts "-------------------------------------------"
#cadena="ls  *.mpg *.AVI"
extension="kkk"
cadena="find  -maxdepth 1 -iname '*.mpg' -not -iname 'dmr_*.*' -o -iname '*.avi' -not -iname 'dmr_*.*'   -type f | sort"   #busca en subdirectorio  
directorylist = %x[#{cadena}]
salida="mencoder  -oac copy -ovc copy "
directorylist.each do |filename|
    f=filename.chomp!
    extension=/(.*\.)(.*$)/.match(f)[2]
    salida=salida + "#{f} "
end 
$output=$output+"."+extension.to_s
salida=salida+" -o #{$output}"
puts salida
system(salida)
puts "-------------------------------------------"
