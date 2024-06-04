function crearDocs() {
    // ID de la carpeta donde se guardarán los documentos creados
    const carpetaId = '1f4NcTe9J3YTlx9OT4i6rJZQe6l7pi_q0';
    // Obtiene la referencia a la carpeta usando el ID
    const carpeta = DriveApp.getFolderById(carpetaId);
    // Obtiene la hoja de cálculo activa
    const sheet = SpreadsheetApp.getActiveSpreadsheet().getActiveSheet();
    // Obtiene todos los datos de la hoja activa
    const filas = sheet.getDataRange().getValues();
  
    // Elimina la primera fila de encabezados para no procesarla
    filas.shift();
  
    // Recorre cada fila de la hoja de cálculo
    filas.forEach((fila, index) => {
      // Verifica si el documento correspondiente ya fue creado
      if (fila[7] !== "Sí") {
        // Define el nombre del documento a crear basado en el contenido de una celda específica
        const docNombre = 'Doc para ' + fila[3];
        // Crea un nuevo documento de Google Docs con el nombre especificado
        const doc = DocumentApp.create(docNombre);
        // Obtiene el cuerpo del documento para poder añadir texto
        const cuerpo = doc.getBody();
  
        // Crea un título en el documento y aplica varios estilos de formato
        let titulo = cuerpo.appendParagraph('Información de Usuario');
        titulo.setHeading(DocumentApp.ParagraphHeading.HEADING1);
        titulo.setAlignment(DocumentApp.HorizontalAlignment.CENTER);
        titulo.setFontSize(18).setBold(true).setForegroundColor('#0000ff');
  
        // Añade párrafos con información específica de la fila, aplicando un tamaño de fuente estándar
        cuerpo.appendParagraph('Nombre: ' + fila[3]).setFontSize(12);
        cuerpo.appendParagraph('Correo: ' + fila[4]).setFontSize(12);
        cuerpo.appendParagraph('Fecha de registro: ' + fila[6]).setFontSize(12);
  
        // Inserta una línea horizontal para separar secciones del documento
        cuerpo.appendHorizontalRule();
  
        // Añade otra sección con un subencabezado
        cuerpo.appendParagraph('Otra Información:').setHeading(DocumentApp.ParagraphHeading.HEADING2).setBold(true);
  
        // Guarda y cierra el documento creado
        doc.saveAndClose();
  
        // Mueve el documento a la carpeta específica y lo remueve del directorio raíz
        const docFile = DriveApp.getFileById(doc.getId());
        carpeta.addFile(docFile);
        DriveApp.getRootFolder().removeFile(docFile);
  
        // Marca la fila en la hoja de cálculo indicando que el documento ha sido creado
        sheet.getRange(index + 2, 8).setValue("Sí");  // +2 ajusta por el índice base 1 de las hojas y la fila de encabezados
      }
    });
  }
  