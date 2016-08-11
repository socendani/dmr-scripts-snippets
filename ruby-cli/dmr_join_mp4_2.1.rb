#!/usr/bin/env ruby 
#@author: Dani Morte
#@version: 2.1
#@use: $ ruby dmr_join_mp4_2.1.rb -h

#globals
$author="Dani Morte 2010"
$version="2.1"
$debug=true
$script=__FILE__

#ARGV[0] or abort "Ajuda: ruby #{$0} -h "
if __FILE__ == $0
    require 'optparse'
    options = { :directori => ".", :output=>"new.mp4", :test=>false, :moving=>false }
    ARGV.options do |opts|
    	script_name = File.basename($0)
    	opts.banner = "\n$ ruby #{script_name} -d directorio -o output.mp4 "
    	opts.separator ""
	    opts.on("-d=RANDOM_CHARS",String, "Directori origen dels fitxers per unir")  {|options[:directori]|}	
	    #opts.on("--test", "Modo test")  {options[:test]=true}
	    #opts.on("--move", "Mover automáticamente sin preguntar")  {options[:moving]=true}
 	    opts.on("-o=RANDOM_CHARS",String, "Nom del fitxer destí") do |a|
	    	options[:output]=a
	    end
	    opts.on("-h", "Visualitza aquesta esta ajuda", "default: -d "+options[:directori]+" -o "+options[:output]+"") { puts opts; exit }
	    opts.separator ""
        opts.parse!
    end
 
end
 
class Mp4BoxJoin
    attr_accessor :test
    def initialize( prefijo="MP4Box" , directorio=".", ficheroFinal="final" ) 
        @test=true
        @directorio=directorio
        @prefijo=prefijo
        @output=ficheroFinal
        @pageSize=20
        @pages=0
        @extension=".mp4"
        @directoylist={}
        self.cargarDirectorio(@directorio)
        #@totalFicheros=totalFicheros @arrayFicheros
        #puts @totalFicheros.to_s
    end 
    
                
    def cargarDirectorio (dir)
		    cadena="find #{dir} -maxdepth 1 -iname '*#{@extension}' ! -iname 'dmr_*#{@extension}' -type f | sort"   #busca en subdirectorio
		    #puts cadena
		    ficheros=%x[#{cadena}]
		  
            @arrayFicheros=[]
            ficheros.each do |filename|
                f=filename.chomp!
                @arrayFicheros << " '#{f}'"
            end
            @totalFicheros=@arrayFicheros.size
           
    end 
    
    def compositeSalida (ficheros, output)
        output=output.gsub(/.mp4/i,"")		
		#@output+="_"+Time.now.usec.to_s
		output+=".mp4"
		output=output.gsub(/.mp4.mp4/i,".mp4")
        return @prefijo+' -flat'+ ficheros + ' -new ' + output
    end
    def moverFicheros (ficheros)
        #s="mkdir #{@directorio}/processats"
        s="[ -d #{@directorio}/processats ] || mkdir #{@directorio}/processats"  #crea el directori NOMES si no existeix
        system(s)
        s="mv #{ficheros} #{@directorio}/processats"
    end
    def run
		totalPages=@totalFicheros / @pageSize
        lastItems=@totalFicheros % @pageSize
		
		
        puts "-------------------------------------------"
        puts "Concatena videos *.mpg manteniendo ratios. situarse en la carpeta y ejecutar ruby app.rb"
        puts "Author: "+$author 
        puts "Dependences: MP4Box  (sudo apt-get install gpac)"
		puts "Script: "+$script+" (#{$version})"		
		puts "Directorio: "+@directorio
		puts "Paginación: "+@pageSize.to_s
		puts "Ficheros temporales necesarios: #{totalPages} y #{lastItems}"
		puts "Total Ficheros a unir (*#{@extension}): #{@totalFicheros} "
		puts "-------------------------------------------"
		
		page=1;count=1;index=0;
		 #puts @arrayFicheros
		
		while page <= totalPages
		    counts="#{count}".to_i+9   #TODO mejorar este punto es para mantener el orden cuando sean mas de 10
		    output="dmr_temp#{counts}#{@extension}"
		    
		    count2=1;ficheros_usados="";mp4box_args=""
		    while count2 <= @pageSize
		        f=@arrayFicheros[index]
		        mp4box_args="#{mp4box_args} -cat#{f}" 
		        ficheros_usados=ficheros_usados.to_s + " #{f}" 
		        count2+=1;index+=1
		    end
	        page +=1;count+=1
	        
	        #montamos la salida temporal
	        salida=compositeSalida(mp4box_args, output)
	        puts salida
	        if @test==false then system(salida) end
	        salida=moverFicheros(ficheros_usados)
	        #puts salida
	        if @test==false then system(salida) end
		    
		end
		#para el resto en la division
		if lastItems > 0
		    counts="#{count}".to_i+9   #TODO mejorar este punto es para mantener el orden cuando sean mas de 10
		    output="dmr_temp#{counts}#{@extension}"
		    if totalPages==0 then output=@output end
		    count2=1;ficheros_usados="";mp4box_args=""
		    while count2 <= lastItems
		        f=@arrayFicheros[index]
		        mp4box_args="#{mp4box_args} -cat#{f}" 
		        ficheros_usados=ficheros_usados.to_s + " #{f}" 
		        count2+=1;index+=1
		    end
	        page +=1;count+=1
	        
	        #montamos la salida temporal
	        salida=compositeSalida(mp4box_args, output)
	        puts salida
	        if @test==false then system(salida) end
	        salida=moverFicheros(ficheros_usados)
	        #puts salida
	        if @test==false then system(salida) end
		    
		end
		
		#Una vez provcesado los temporales.. hacemos el final, uniendo los temporales
		if totalPages>0 then 
		    mp4box_args=""
		    output=@output
            %x[ls dmr_temp*].each do |filename|
                f=filename.chomp!
		        mp4box_args="#{mp4box_args} -cat '#{f}'" 
		    end
		    salida=compositeSalida(mp4box_args, output)
	        puts salida
	        if @test==false then system(salida) end
	        salida="rm dmr_temp*.mp4"
	        puts salida
	        if @test==false then system(salida) end
	   end
	   puts "-------------------------------------------"
    end
    
       
end





obj=Mp4BoxJoin.new("MP4Box", options[:directori], options[:output])
obj.test=false
obj.run


