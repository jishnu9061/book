module.exports = {
    methods: {
        /**
         * Translate the given key.
         */
        __(key, replace = {}) {
            // Ensure key is a string and not undefined/null
            if (!key || typeof key !== 'string') {
                return key || ''
            }

            // Ensure language object exists
            if (!this.$page || !this.$page.props || !this.$page.props.language) {
                return key
            }

            var translation = this.$page.props.language[key]
                ? this.$page.props.language[key]
                : key

            // Ensure translation is a string before calling replace
            if (typeof translation !== 'string') {
                return key
            }

            Object.keys(replace).forEach(function (replaceKey) {
                if (typeof replace[replaceKey] === 'string') {
                    translation = translation.replace(':' + replaceKey, replace[replaceKey])
                }
            });

            return translation
        },
    },
}
