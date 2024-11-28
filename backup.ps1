<#
.Synopsis
   
   Script creado para backup de carpeta de proyecto laravel y bd mariadb.

.DESCRIPTION
   
   Procesos del script:

    - Primera fase vuelca backup de bd a ruta especificada
    - Segunda parte comprime todos los ficheros (proyecto + backup bd) a ruta especificada
   
.NOTES
   
   Autor: Alfredo Muñoz
   Version: 
        0.1 - 27/11/2020.   

#>

#Primera fase backup bd

Write-Output "Comienza backup base de datos"

#------- EDITAR ESTAS VARIABLES --------

$SQLPath = "C:\Program Files\MariaDB 11.5\"
$BackupDirectory = "C:\proyecto_liceo\app\backups_bd"
$User = "root"
$Pass = "LiceoMonjardin24"
$Port = 3306
$Database = "gesliceo"
$proyectpath = "C:\proyecto_liceo\app\gesliceo\"

#---------------------------------------

$BackupDate = Get-Date -Format "%d%M%y%h%m%s"
$Filename = $Database + "_backup_" + $BackupDate

New-Item $BackupDirectory -ItemType Directory -Force | Out-Null

try {
  Set-Location "$SQLPath\bin"
  .\mysqldump.exe -P $Port -u $User -p"$Pass" $Database | Out-File "$BackupDirectory\$Filename.sql" -Encoding Ascii
}
catch {
  Write-Output("Backup Failed!")
}

Write-Output "Backup de base de datos correcto"

#Segunda fase, backup de todos los ficheros

Write-Output "Comienza backup de ficheros"

$compress = @{
  Path = "C:\proyecto_liceo"
  CompressionLevel = "Fastest"
  DestinationPath = $("C:\DATOS\MEGA\backup_gesliceo\backup_") + $(Get-Date -Format "%d%M%y%h%m%s")+$(".zip")
}
Compress-Archive @compress
Set-Location $proyectpath

Write-Output "Backup de ficheros correcto"
Write-Output "Proceso finalizado correctamente"
