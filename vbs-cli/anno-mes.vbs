' Dani Morte 2016
' Usage:   anno-mes.vbs   AÑO   MES   X:\FOLDER


Set args = WScript.Arguments
anno = args.Item(0)
mes = args.Item(1)
folder = args.Item(2)

Dim oFSO
Set oFSO = CreateObject("Scripting.FileSystemObject")

for i=1 to 31
  num="0"+Cstr(i)
  x=right(num,2)
  ruta=Cstr(folder+"\"+anno+"-"+mes+"-"+x)
  oFSO.CreateFolder ruta
' md "ruta"
 'Wscript.echo ruta
next
