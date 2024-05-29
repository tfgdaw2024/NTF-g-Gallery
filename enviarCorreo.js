function enviarCorreoBienvenida() {
  // Obtiene la hoja de cálculo activa
  var sheet = SpreadsheetApp.getActiveSpreadsheet().getActiveSheet();

  // Obtiene todos los datos de la hoja activa
  var rows = sheet.getDataRange().getValues();

  // Elimina la primera fila de encabezados para no procesarla
  rows.shift();

  // Recorre cada fila de la hoja de cálculo
  rows.forEach((row, index) => {
    // Verifica si el correo de bienvenida ya fue enviado
    if (row[10] !== "Sí") { // Suponiendo que la columna 'Emails Sent' está en la columna 11 (índice 10)
      var name = row[3]; // Suponiendo que 'User Nicename' es la cuarta columna (índice 3)
      var email = row[4]; // Suponiendo que 'User Email' es la quinta columna (índice 4)

      // Enviar correo electrónico de bienvenida al usuario
      MailApp.sendEmail({
        to: email,
        subject: 'Bienvenido a NTF-g-Gallery',
        body: 'Hola ' + name + ',\n\nGracias por registrarte en NTF-g-Gallery. Estamos encantados de tenerte con nosotros.\n\nSaludos,\nEl equipo de NTF-g-Gallery'
      });

      // Enviar notificación al administrador
      MailApp.sendEmail({
        to: 'tfgdaw2024@gmail.com',
        subject: 'Nuevo Registro de Usuario',
        body: 'Se ha registrado un nuevo usuario: ' + name + ' (' + email + ').'
      });

      // Marca la fila en la hoja de cálculo indicando que el correo ha sido enviado
      sheet.getRange(index + 2, 11).setValue("Sí");  // +2 ajusta por el índice base 1 de las hojas y la fila de encabezados
    }
  });
}

