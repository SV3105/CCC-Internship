const state = {
  view: 'interns',
  loading: false,
  error: null,

  interns: [],
  tasks: [],
  logs: [],

  sequence: 1
};

function setState(updater) {
  updater(state);
  render();
}

function logAction(message) {
  state.logs.push({
    message,
    time: new Date().toISOString()
  });
}
