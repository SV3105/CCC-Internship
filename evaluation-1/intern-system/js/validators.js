function validateIntern(data) {
  if (!data.name || !data.email) {
    throw new Error('Name and Email required');
  }
}

function validateTask(data) {
  if (!data.title) {
    throw new Error('Task title required');
  }
}
