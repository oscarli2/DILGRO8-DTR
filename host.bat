@move /y %~dp0hosts "C:\Windows\System32\drivers\etc\" && (
  @echo "File has been moved..."
  (call )
) || (
  @echo "Error when moving file(s). Please run this app as Administrator."
)
@timeout /t 3