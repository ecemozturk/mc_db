// ability.js
import { defineAbility, Ability } from '@casl/ability';

function subjectName(subject) {
  if (!subject || typeof subject === 'string') {
    return subject;
  }
  return subject.__type;
}

const initialAbility = new Ability([], { subjectName });

const guestAbility = defineAbility((can, cannot) => {
  // Define abilities for guest users
  can('read', 'Auth');
});

const adminAbility = defineAbility((can, cannot) => {
  // Define abilities for admin users
  can('manage', 'all');
});

// ... other code


export const checkPermission = (action, subject) => {
  const ability = localStorage.getItem('userAbilities')
      ? new Ability(JSON.parse(localStorage.getItem('userAbilities')))
      : guestAbility

  return ability.can(action, subject)
}

const can = (action, subject) => {
  const ability = localStorage.getItem('userAbilities')
      ? new Ability(JSON.parse(localStorage.getItem('userAbilities')), { subjectName })
      : guestAbility

  return ability.can(action, subject)
}


export { adminAbility, guestAbility, initialAbility, can }

