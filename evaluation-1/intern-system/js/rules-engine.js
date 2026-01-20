function generateInternId() {
  const year = new Date().getFullYear();
  return `${year}-${state.sequence++}`;
}

function canTransition(from, to) {
  const rules = {
    ONBOARDING: ['ACTIVE'],
    ACTIVE: ['EXITED'],
    EXITED: []
  };
  return rules[from].includes(to);
}

function createTask(data) {
  // basic validation
  if (!data.title) {
    throw new Error("Task title is required");
  }

  const task = {
    id: Date.now(), // simple unique id
    title: data.title,
    requiredSkills: data.requiredSkills || [],
    dependencies: data.dependencies || [],
    status: "OPEN",
    assignedTo: null,
    hours: data.hours || 0
  };

  // circular dependency check
  if (hasCircularDependency(task, task.dependencies)) {
    throw new Error("Circular dependency detected");
  }

  setState(state => {
    state.tasks.push(task);
  });

  logAction(`Task created: ${task.title}`);
}

function hasCircularDependency(task, dependencies, visited = new Set()) {
  for (let depId of dependencies) {
    if (depId === task.id) return true;

    if (visited.has(depId)) continue;
    visited.add(depId);

    const depTask = state.tasks.find(t => t.id === depId);
    if (depTask) {
      if (hasCircularDependency(task, depTask.dependencies, visited)) {
        return true;
      }
    }
  }
  return false;
}


function assignTask(taskId, internId) {
  const intern = state.interns.find(i => i.id === internId);
  const task = state.tasks.find(t => t.id === taskId);

  if (intern.status !== 'ACTIVE') {
    throw new Error('Only ACTIVE interns can receive tasks');
  }

  if (task.assignedTo === internId) {
    throw new Error('Task already assigned');
  }

  task.assignedTo = internId;
  logAction(`Task ${task.title} assigned to ${intern.name}`);
}

function markTaskDone(taskId) {
  const task = state.tasks.find(t => t.id === taskId);
  if (!task) throw new Error("Task not found");

  // check dependencies
  const pendingDeps = task.dependencies.filter(depId => {
    const dep = state.tasks.find(t => t.id === depId);
    return dep && dep.status !== "DONE";
  });

  if (pendingDeps.length > 0) {
    throw new Error("Dependencies not completed");
  }

  task.status = "DONE";

  logAction(`Task completed: ${task.title}`);

  // auto-update dependent tasks
  autoUpdateTasks();
}

function autoUpdateTasks() {
  state.tasks.forEach(task => {
    if (task.status === "OPEN" && task.dependencies.length > 0) {
      const allDone = task.dependencies.every(depId => {
        const dep = state.tasks.find(t => t.id === depId);
        return dep && dep.status === "DONE";
      });

      if (allDone) {
        task.status = "READY";
        logAction(`Task ready: ${task.title}`);
      }
    }
  });
}

function updateInternStatus(internId, newStatus) {
  const intern = state.interns.find(i => i.id === internId);

  if (!intern) {
    alert("Intern not found");
    return;
  }

  // Rule enforcement
  if (intern.status === "EXITED" && newStatus === "ACTIVE") {
    alert("Cannot activate an exited intern");
    return;
  }

  // Allowed transitions
  const allowedTransitions = {
    "ONBOARDING": ["ACTIVE"],
    "ACTIVE": ["EXITED"]
  };

  if (!allowedTransitions[intern.status]?.includes(newStatus)) {
    alert(`Cannot change from ${intern.status} to ${newStatus}`);
    return;
  }

  intern.status = newStatus;
  logAction(`Intern "${intern.name}" status changed to ${newStatus}`);
  render();
}

