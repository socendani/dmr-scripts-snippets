#!/usr/bin/env ruby 
#@author: Dani Morte
#@version: 2.1
#@use: $ ruby dmr_join_mp4_2.1.rb -h

#globals
$version="2.1"
$debug=true
$script=__FILE__

ARGV[0] or abort "Ajuda: ruby #{$0} -h "
if __FILE__ == $0
  require 'optparse'
  options = { :directori => ".", :output=>"new.mp4" }
  ARGV.options do |opts|
    	script_name = File.basename($0)
    	opts.banner = "\n$ ruby #{script_name} -d directorio -o output.mp4 "
    	opts.separator ""
	opts.on("-d=RANDOM_CHARS",String, "Directori origen dels fitxers per unir")  {|options[:directori]|}	
 	opts.on("-o=RANDOM_CHARS",String, "Nom del fitxer destí") do |a|
		options[:output]=a
	end
	opts.on("-h", "Visualitza aquesta esta ajuda", "default: -d "+options[:directori]+" -o "+options[:output]+"") { puts opts; exit }
	opts.separator ""
        opts.parse!
  end
 
end
 
 
 
class JoinMP4 
 
	def initialize (output="new.mp4")
		@salida="MP4Box"
		@output=output
	end
 
	def cargardirectorio (dir)
		@directori=dir
		cadena="find #{dir} -name '*.??4' | sort"   #busca en subdirectorio y subdirecorio
		cadena="find #{dir} -maxdepth 1 -name '*.??4' -type f | sort"   #busca en subdirectorio
		#cadena="ls #{dir} '*.??4' "
		#puts cadena
		@directorylist = %x[#{cadena}]
		self.prepararsalida(@directorylist) 
	end
 
	def prepararsalida (ficheros) 
		c=0
		@ficheros_concat=""	
		ficheros.each do |filename|
			if c==0 then @salida+= " -flat" end  #poniendo este flag va más rápido y mantiene aspecto del video
			##if c>=18 then break end	
			if c<20 
				f=filename.chomp!			
				@salida+=" -cat '#{f}'"
				@ficheros_concat=@ficheros_concat.to_s + " '#{f}'"  #pensado para mover/eliminar los YA procesados
			end		
			c=c+1		
 
		end
		if c>=20
		    puts " En esta carpera hay #{c} ficheros.. \ndeberás repetir el proceso, pues MP4Box solo puede procesar 20 ficheros " 
		end	
		@totalficheros=c
	end
 
	def ficherosalida(output)
		@output=output
	end
	def moverficheros()
		s="mkdir #{@directori}/processats"
		system(s)
		#puts s
		s="mv #{@ficheros_concat} #{@directori}/processats"
		system(s)
		puts "....Movent a processats .... OK"	
	end
	def eliminarficheros()
		print ("¿estas segur que els vols eliminar (s/n)?")
		seguro=gets().chomp
		if (seguro=="s")||(seguro="y")
			s="rm #{@ficheros_concat} "
			system(s)
			puts "... ELIMINANDO .. OK..."
 
		end	
 
 
	end
 
	def run
 
		@output=@output.gsub(/.mp4/i,"")		
		@output+="_"+Time.now.usec.to_s
		@output+=".mp4"
		@output=@output.gsub(/.mp4.mp4/i,".mp4")
 
		puts "-------------------------------------------"
		puts "Script: "+$script+" (#{$version})"		
		puts "Directorio: "+@directori
		puts "Output: "+@output
		puts "Total Ficheros (*.mp4): #{@totalficheros} "
		puts "-------------------------------------------"
		if @totalficheros==0  then abort " No hi han fitxers per a processar.." end
		@salida=@salida+" -new #{@output}  "
		puts @salida
 
		#ok=system(@salida)
		ok=false
		if ok
		  	print('Sobre els fitxers PROCESSATS, pots:  (e)liminar, (m)oure, (altres) No fer res: ' )
			accion = gets().chomp
			case accion.to_s
			   when "d","e"  
				self.eliminarficheros()
			   when "m"  
				self.moverficheros()
 
			   else 
				puts "...finalitzat.."
			end
		end
		puts "-------------------------------------------"
		puts "Fi del Proces"
	end
end
 
 
 
obj=JoinMP4.new
obj.cargardirectorio options[:directori]
obj.ficherosalida options[:output]
obj.run
