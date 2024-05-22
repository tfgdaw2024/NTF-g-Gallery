function importarDatos() {
    // URL del script PHP que retorna datos en formato JSON
    var url = "https://tfgdaw.dreamhosters.com/script.php";
    
    // Realiza una solicitud HTTP a la URL y obtiene la respuesta
    var respuesta = UrlFetchApp.fetch(url);
    
    // Convierte la respuesta de texto JSON a un objeto JavaScript
    var datos = JSON.parse(respuesta.getContentText());
  
    // Obtiene la hoja de cálculo activa de Google Sheets
    var hoja = SpreadsheetApp.getActiveSpreadsheet().getActiveSheet();
    
    // Define las cabeceras que se usarán en la hoja de cálculo
    var cabeceras = ["ID", "User Login", "User Pass", "User Nicename", "User Email", "User URL", "User Registered", "User Activation Key", "User Status", "Docs Created"];
  
    // Comprueba si la primera fila está vacía y, de ser así, establece las cabeceras
    if (hoja.getRange(1, 1, 1, cabeceras.length).getValues().flat().join("") === "") {
      hoja.getRange(1, 1, 1, cabeceras.length).setValues([cabeceras]);
    }
  
    // Obtiene todos los IDs existentes para evitar duplicar datos
    var lastRow = hoja.getLastRow();
    var idsExistentes = [];
    if (lastRow >= 2) {
      idsExistentes = hoja.getRange(2, 1, lastRow - 1, 1).getValues().map(function(row){ 
        return row[0].toString(); // Convierte cada ID a string para comparaciones consistentes
      });
    }
  
    // Filtra los datos para incluir solo aquellos que no están ya en la hoja
    var datosFiltrados = datos.filter(function (registro) {
      return idsExistentes.indexOf(registro.ID.toString()) === -1; // Solo agrega registros con ID no listado
    });
  
    // Convierte los registros filtrados a un formato adecuado para Google Sheets
    var arrayDatos = datosFiltrados.map(objeto => [
      objeto.ID.toString(), // Convierte ID a string para consistencia
      objeto.user_login,
      objeto.user_pass,
      objeto.user_nicename,
      objeto.user_email,
      objeto.user_url,
      objeto.user_registered,
      objeto.user_activation_key,
      objeto.user_status,
      objeto.display_name
    ]);
  
    // Escribe los nuevos datos en la hoja si se encontraron registros nuevos
    if (arrayDatos.length > 0) {
      var primeraFilaVacia = hoja.getLastRow() + 1; // Encuentra la primera fila vacía después de la última usada
      hoja.getRange(primeraFilaVacia, 1, arrayDatos.length, arrayDatos[0].length).setValues(arrayDatos);
    } else {
      // Mensaje de log si no hay datos nuevos para agregar
      console.log("No hay datos nuevos o los datos no están en el formato esperado.");
    }
  }
  