document.querySelectorAll('nav button').forEach(btn => {
  btn.onclick = () => {
    setState(s => s.view = btn.dataset.view);
  };
});

// Expose markTaskDone to global scope for buttons
window.markTaskDone = function(taskId) {
  if (!confirm("Are you sure you want to mark this task DONE?")) return;
  try {
    markTaskDone(taskId);
    render();
  } catch (err) {
    alert(err.message);
  }
};




async function createIntern(data) {
  // 1. Basic validation (sync)
  validateIntern(data);

  // 2. Async email uniqueness check (fake backend)
  const isUnique = await checkEmailUnique(data.email);
  if (!isUnique) {
    throw new Error("Email already exists");
  }

  // 3. Create intern object (business rules)
  const intern = {
    id: generateInternId(),
    name: data.name,
    email: data.email,
    skills: data.skills || [],
    status: "ONBOARDING"
  };

  // 4. Update centralized state
  setState(state => {
    state.interns.push(intern);
  });

  // 5. Audit log
  logAction(`Intern created: ${intern.name}`);
}

function assignTaskToIntern(taskId, internId) {
  const task = state.tasks.find(t => t.id === taskId);
  const intern = state.interns.find(i => i.id === internId);

  if (!task || !intern) {
    alert("Invalid task or intern");
    return;
  }

  if (intern.status !== "ACTIVE") {
    alert("Only ACTIVE interns can be assigned tasks");
    return;
  }

  if (task.assignedTo) {
    alert("Task already assigned");
    return;
  }

  const hasAllSkills = task.requiredSkills.every(skill =>
    intern.skills.includes(skill)
  );

  if (!hasAllSkills) {
    alert("Intern does not have required skills");
    return;
  }

  task.assignedTo = intern.id;
  logAction(`Task "${task.title}" assigned to ${intern.name}`);
  render();
}


// Seed data
// ---- Seed Interns ----
setState(s => {
  s.interns.push(
    {
      id: generateInternId(),
      name: 'Sneha Vaghela',
      email: 'sneha@test.com',
      skills: ['JS', 'CSS'],
      status: 'ONBOARDING'
    },
    {
      id: generateInternId(),
      name: 'Rahul Sharma',
      email: 'rahul@test.com',
      skills: ['HTML', 'CSS'],
      status: 'ACTIVE'
    },
    {
      id: generateInternId(),
      name: 'Anita Patel',
      email: 'anita@test.com',
      skills: ['JS', 'React'],
      status: 'ACTIVE'
    },
    {
      id: generateInternId(),
      name: 'Vikram Singh',
      email: 'vikram@test.com',
      skills: ['HTML', 'CSS', 'JS'],
      status: 'EXITED'
    }
  );
});

// ---- Seed Tasks ----
setState(s => {
  s.tasks.push(
    {
      id: 101,
      title: 'Build Landing Page',
      requiredSkills: ['HTML', 'CSS'],
      status: 'OPEN',
      dependencies: [],
      hours: 5,
      assignedTo: s.interns.find(i => i.name === 'Rahul Sharma')?.id || null
    },
    {
      id: 102,
      title: 'Create JS Form Validation',
      requiredSkills: ['JS'],
      status: 'OPEN',
      dependencies: [101], // depends on Landing Page task
      hours: 3,
      assignedTo: s.interns.find(i => i.name === 'Anita Patel')?.id || null
    },
    {
      id: 103,
      title: 'Build React Component',
      requiredSkills: ['React', 'JS'],
      status: 'OPEN',
      dependencies: [],
      hours: 4,
      assignedTo: null
    },
    {
      id: 104,
      title: 'CSS Animations',
      requiredSkills: ['CSS'],
      status: 'OPEN',
      dependencies: [],
      hours: 2,
      assignedTo: null
    },
    {
      id: 105,
      title: 'Final Testing',
      requiredSkills: ['JS', 'HTML', 'CSS'],
      status: 'OPEN',
      dependencies: [101, 102, 103, 104], // depends on all previous tasks
      hours: 6,
      assignedTo: null
    }
  );
});

// ---- Seed Logs ----
setState(s => {
  s.logs.push(
    { time: new Date().toLocaleTimeString(), message: 'System initialized' },
    { time: new Date().toLocaleTimeString(), message: 'Seed data added' }
  );
});
