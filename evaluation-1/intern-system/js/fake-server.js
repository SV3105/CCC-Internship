function fakeDelay(result, fail = false) {
  return new Promise((resolve, reject) => {
    setTimeout(() => {
      fail ? reject(result) : resolve(result);
    }, 600);
  });
}

function checkEmailUnique(email) {
  const exists = state.interns.some(i => i.email === email);
  return fakeDelay(!exists);
}
