function createDocs() {
    // ID de la carpeta donde se guardarán los documentos creados
    const folderId = '1f4NcTe9J3YTlx9OT4i6rJZQe6l7pi_q0';
    // Obtiene la referencia a la carpeta usando el ID
    const folder = DriveApp.getFolderById(folderId);
    // Obtiene la hoja de cálculo activa
    const sheet = SpreadsheetApp.getActiveSpreadsheet().getActiveSheet();
    // Obtiene todos los datos de la hoja activa
    const rows = sheet.getDataRange().getValues();
  
    // Elimina la primera fila de encabezados para no procesarla
    rows.shift();
  
    // Recorre cada fila de la hoja de cálculo
    rows.forEach((row, index) => {
      // Verifica si el documento correspondiente ya fue creado
      if (row[7] !== "Sí") {
        // Define el nombre del documento a crear basado en el contenido de una celda específica
        const docName = 'Doc para ' + row[3];
        // Crea un nuevo documento de Google Docs con el nombre especificado
        const doc = DocumentApp.create(docName);
        // Obtiene el cuerpo del documento para poder añadir texto
        const body = doc.getBody();
  
        // Crea un título en el documento y aplica varios estilos de formato
        let title = body.appendParagraph('Información de Usuario');
        title.setHeading(DocumentApp.ParagraphHeading.HEADING1);
        title.setAlignment(DocumentApp.HorizontalAlignment.CENTER);
        title.setFontSize(18).setBold(true).setForegroundColor('#0000ff');
  
        // Añade párrafos con información específica de la fila, aplicando un tamaño de fuente estándar
        body.appendParagraph('Nombre: ' + row[3]).setFontSize(12);
        body.appendParagraph('Correo: ' + row[4]).setFontSize(12);
        body.appendParagraph('Fecha de registro: ' + row[6]).setFontSize(12);
  
        // Inserta una línea horizontal para separar secciones del documento
        body.appendHorizontalRule();
  
        // Añade otra sección con un subencabezado
        body.appendParagraph('Otra Información:').setHeading(DocumentApp.ParagraphHeading.HEADING2).setBold(true);
  
        // Guarda y cierra el documento creado
        doc.saveAndClose();
  
        // Mueve el documento a la carpeta específica y lo remueve del directorio raíz
        const docFile = DriveApp.getFileById(doc.getId());
        folder.addFile(docFile);
        DriveApp.getRootFolder().removeFile(docFile);
  
        // Marca la fila en la hoja de cálculo indicando que el documento ha sido creado
        sheet.getRange(index + 2, 8).setValue("Sí");  // +2 ajusta por el índice base 1 de las hojas y la fila de encabezados
      }
    });
  }
  