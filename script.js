// Inicializar la matriz de estado de los boxes
var boxes = [  {id: 1, status: 'disponible', currentClient: null},  {id: 2, status: 'disponible', currentClient: null}];

// Inicializar la matriz de clientes en espera
var waitingClients = [];

// Función para asignar un cliente a un box disponible
function assignClientToBox(client) {
  // Verificar si hay un box disponible
  var availableBox = boxes.find(box => box.status === 'disponible');

  if (availableBox) {
    // Asignar el cliente al box disponible
    availableBox.currentClient = client;
    availableBox.status = 'ocupado';

    // Actualizar el visor con el número de turno y el box asignado
    updateVisor(client, availableBox.id);
  } else {
    // No hay boxes disponibles, así que agregar el cliente a la lista de espera
    waitingClients.push(client);
  }
}

// Función para actualizar el estado de un box cuando se libera
function releaseBox(boxId) {
  var releasedBox = boxes.find(box => box.id === boxId);

  // Marcar el box como disponible
  releasedBox.status = 'disponible';
  releasedBox.currentClient = null;

  // Verificar si hay clientes en espera
  if (waitingClients.length > 0) {
    // Asignar el siguiente cliente en espera al box disponible
    var nextClient = waitingClients.shift();
    assignClientToBox(nextClient);
  }
}

// Función para procesar la elección del veterinario
function selectVeterinarian(veterinarian) {
  // Generar un número de turno para el cliente
  var clientNumber = generateClientNumber();

  // Crear un objeto de cliente con el número de turno y el veterinario seleccionado
  var client = {number: clientNumber, veterinarian: veterinarian};

  if (veterinarian === 1 || veterinarian === 2) {
    // Si se selecciona el veterinario 1 o 2, asignar al cliente al box correspondiente
    assignClientToBox(client);
  } else if (veterinarian === 3) {
    // Si se selecciona el veterinario 3, asignar al cliente al primer box disponible
    var availableBox = boxes.find(box => box.status === 'disponible');

    if (availableBox) {
      // Asignar el cliente al
// Si hay un box disponible, asignar el cliente a ese box
availableBox.currentClient = client;
      availableBox.status = 'ocupado';

      // Actualizar el visor con el número de turno y el box asignado
      updateVisor(client, availableBox.id);
    } else {
      // Si ambos boxes están ocupados, asignar aleatoriamente al cliente a uno de ellos
      var randomBox = Math.floor(Math.random() * boxes.length);
      assignClientToBox(client, boxes[randomBox].id);
    }
  }
}

// Función para generar un número de turno único
function generateClientNumber() {
  // Aquí debes implementar tu propia lógica para generar un número de turno único
  // Puede ser un número secuencial o basado en la hora actual, por ejemplo
}

// Función para actualizar el visor con el número de turno y el box asignado
function updateVisor(client, boxId) {
  // Aquí debes implementar tu propia lógica para actualizar el visor con el número de turno y el box asignado
  // Puede ser mediante una llamada a una API o actualizando directamente el HTML de la página, por ejemplo
}

// Función para procesar la liberación de un box
function releaseBox(boxId) {
  // En esta función debes implementar la lógica para liberar el box con el ID especificado
  // Esto podría incluir cambiar el estado del box a 'disponible', establecer el cliente actual en null, y verificar si hay clientes en espera para asignarles el box liberado
}

// Función para procesar el botón 'ocupado' de un box
function setBoxOccupied(boxId) {
  // En esta función debes implementar la lógica para establecer el estado de un box a 'ocupado'
  // Esto podría incluir cambiar el estado del box y actualizar el visor para reflejar que el box está ocupado
}

// Función para procesar el botón 'desocupado' de un box
function setBoxAvailable(boxId) {
  // En esta función debes implementar la lógica para establecer el estado de un box a 'disponible' y liberar el cliente actual asignado al box
  // Esto podría incluir cambiar el estado del box, actualizar el visor para reflejar que el box está disponible, y liberar al cliente actual asignado al box
  releaseBox(boxId);
}