' Dani 2001
' \\pandora\departaments\mapeo.bat dani

Set args = WScript.Arguments

arg1 = args.Item(0)

Set WSHNetwork = CreateObject("WScript.Network")
Set clDrives = WSHNetwork.EnumNetworkDrives
For i = 0 to clDrives.Count -1 Step 2
	WSHNetwork.RemoveNetworkDrive clDrives.Item(i), True, True
Next


'msgbox "Mapeo de Unidades de Red (only Windows)"

mapear "T:", "\\comunicacions\Temporal", "T: Unidad Temporal de traspaso. Se elimina periódicamente"
mapear "L:", "\\comunicacions\Personals\" & arg1, "L: Unidad Personal. Guardar el trabajo diario"
mapear "P:", "\\comunicacions\Projectes", "P: Unidad de proyectos activos"
mapear "Q:", "\\comunicacions\Departaments", "Q: Unidad departamental para guardar el trabajo común"
mapear "S:", "\\comunicacions\Software", "S: Acceso al repositorio de Software"
mapear "K:", "\\comunicacions\Escaner", "K: Carpeta de trabajos escaneador en la impresora Konika"

'net use N: \\pandora\Temporal /user:domprueba\user01 pass123 /PERSISTENT:YES

Function mapear( letra, camino, descripcion)
   Set objNetwork = CreateObject("WScript.Network") 
   'objNetwork.RemoveNetworkDrive letra, True, True
   objNetwork.MapNetworkDrive letra, camino , True
   
   Set objShell = CreateObject("Shell.Application")
   objShell.NameSpace(letra).Self.Name = descripcion

End function

WScript.Quit 


