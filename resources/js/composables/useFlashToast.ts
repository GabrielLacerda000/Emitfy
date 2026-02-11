import { usePage } from '@inertiajs/vue3'
import { watch } from 'vue'
import { toast } from 'vue-sonner'

type FlashMessage = {
    message: string | null
    type: 'success' | 'error' | 'info' | 'warning'
}

export function useFlashToast() {
    const page = usePage()

    if (!page.props) {
        console.warn('useFlashToast: page.props not available yet')
        return
    }

    // Track last shown message to prevent duplicates on navigation
    let lastMessage = ''

    watch(
        () => page.props.flash as FlashMessage,
        (flash) => {
            if (!flash?.message) return

            // Prevent showing the same toast multiple times
            if (flash.message === lastMessage) return

            lastMessage = flash.message

            // Trigger appropriate toast based on type
            switch (flash.type) {
                case 'success':
                    toast.success(flash.message)
                    break
                case 'error':
                    toast.error(flash.message)
                    break
                case 'warning':
                    toast.warning(flash.message)
                    break
                case 'info':
                default:
                    toast.info(flash.message)
                    break
            }
        },
        { deep: true, immediate: true }
    )
}
