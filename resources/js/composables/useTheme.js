import { ref, onMounted, onUpdated } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function useTheme() {
    const page = usePage()
    
    // Reactive data
    const currentMode = ref('light')
    const currentDir = ref('ltr')
    const enableOption = ref({ color_picker: false })

    // Methods
    const scrollFunction = () => {
        const mybutton = document.getElementById("back-to-top")
        if (mybutton != null) {
            if (document.body.scrollTop > 500 || document.documentElement.scrollTop > 500) {
                mybutton.classList.add("block")
                mybutton.classList.remove("hidden")
            } else {
                mybutton.classList.add("hidden")
                mybutton.classList.remove("block")
            }
        }
    }

    const topFunction = () => {
        document.body.scrollTop = 0
        document.documentElement.scrollTop = 0
    }

    const switchMode = () => {
        currentMode.value = currentMode.value === 'light' ? 'dark' : 'light'
        localStorage.setItem('current_mode', currentMode.value)
        changeTheme()
    }

    const changeTheme = () => {
        const htmlTag = document.getElementsByTagName("html")[0]
        htmlTag.className = currentMode.value
    }

    const changeDir = () => {
        const htmlTag = document.getElementsByTagName("html")[0]
        htmlTag.dir = currentDir.value
    }

    const actionColorScheme = (e) => {
        e.preventDefault()
        const that = e.currentTarget
        const dataExpend = that.getAttribute('data-expend')
        if (dataExpend !== 'yes') {
            that.setAttribute('style', 'right:-10px !important')
            that.setAttribute('data-expend', 'yes')
        } else {
            that.setAttribute('style', 'right:-154px !important')
            that.setAttribute('data-expend', 'no')
        }
    }

    const setColorScheme = (e) => {
        e.preventDefault()
        const color = e.currentTarget.getAttribute('data-scheme')
        const colors = [
            'scheme-indigo', 'scheme-orange', 'scheme-amber', 'scheme-yellow', 
            'scheme-lime', 'scheme-green', 'scheme-cyan', 'scheme-sky', 
            'scheme-violet', 'scheme-purple', 'scheme-fuchsia', 'scheme-pink', 'scheme-rose'
        ]
        document.getElementsByTagName("body")[0].classList.remove(...colors)
        document.getElementsByTagName("body")[0].classList.add(color)
        localStorage.setItem("scheme", color)
    }

    const setColorSchemeFromStorage = () => {
        if (localStorage.getItem("scheme")) {
            const colors = [
                'scheme-indigo', 'scheme-orange', 'scheme-amber', 'scheme-yellow', 
                'scheme-lime', 'scheme-green', 'scheme-cyan', 'scheme-sky', 
                'scheme-violet', 'scheme-purple', 'scheme-fuchsia', 'scheme-pink', 'scheme-rose'
            ]
            document.getElementsByTagName("body")[0].classList.remove(...colors)
            document.getElementsByTagName("body")[0].classList.add(localStorage.getItem("scheme"))
        }
    }

    const initializeTheme = () => {
        setColorSchemeFromStorage()
        
        if (localStorage.getItem('current_dir')) {
            currentDir.value = localStorage.getItem('current_dir')
            changeDir()
        }
        
        if (page.props.enable_options) {
            const options = JSON.parse(page.props.enable_options.value)
            options.forEach(option => {
                enableOption.value[option.slug] = !!option.value
            })
        }
    }

    const setupScrollListener = () => {
        window.onscroll = scrollFunction
    }

    // Lifecycle
    onMounted(() => {
        initializeTheme()
        setupScrollListener()
    })

    onUpdated(() => {
        setColorSchemeFromStorage()
    })

    return {
        // Reactive data
        currentMode,
        currentDir,
        enableOption,

        // Methods
        scrollFunction,
        topFunction,
        switchMode,
        changeTheme,
        changeDir,
        actionColorScheme,
        setColorScheme,
        setColorSchemeFromStorage,
        initializeTheme,
        setupScrollListener
    }
}


