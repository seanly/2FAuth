import { defineStore } from 'pinia'
import { useUserStore } from '@/stores/user'
import groupService from '@/services/groupService'

export const useGroups = defineStore({
    id: 'groups',

    state: () => {
        return {
            items: [],
        }
    },

    getters: {
        current(state) {
            const group = state.items.find(item => item.id === parseInt(useUserStore().preferences.activeGroup))

            return group ? group.name : trans('commons.all')
        }
    },

    actions: {

        /**
         * Fetches the groups collection from the backend
         */
        async fetch() {
            await groupService.getAll().then(response => {
                this.items = response.data
            })
        },

    },
})