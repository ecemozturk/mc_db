import { computed } from 'vue'
import { useAbility } from '@casl/vue'
import {createAbility} from "@/plugins/casl/createAbility";


const ability = createAbility();


export function useAppAbility() {
    const ability = useAbility()

    const can = (action, subject) => {
        return computed(() => ability.can(action, subject))
    }

    return {
        can,
    }
}

export { ability }

export { createAbility }; // Export createAbility
