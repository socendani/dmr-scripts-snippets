#@author: Dani Morte
#@version: 1.0
#@use: $ ruby dmr_join.rb directorio
output="new.mp4"
if ARGV.size > 0
        dir=ARGV[0]
end
#recorremos el directorio y concatenamos los ficheros
cadena="find #{dir} -name '*.??4' | sort"
directorylist = %x[#{cadena}]
salida="MP4Box "
directorylist.each do |filename|
	salida=salida + "-cat #{filename.chomp!} "
end
salida=salida+" -new #{output}"
puts salida
#exec(salida)
