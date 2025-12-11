<template>
    <div @click="copy" class="copy-button">
        <slot>{{ name }}</slot>
    </div>
</template>

<script>
export default {
    name: "ClipboardButton",

    props: {
        name: {
            type: String,
            required: true,
        },
        text: {
            type: String,
            required: true,
        }
    },

    methods: {
        async copy() {
            try {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(this.text);
                    this.$emit("copied");
                    return;
                }

                const textarea = document.createElement("textarea");
                textarea.value = this.text;
                textarea.style.position = "fixed";
                textarea.style.left = "-9999px";
                document.body.appendChild(textarea);

                textarea.select();
                const success = document.execCommand("copy");
                document.body.removeChild(textarea);

                if (success) {
                    alert(this.name + " szavazó linkjének másolása sikeres.");
                    this.$emit("copied");
                } else {
                    throw new Error("Fallback copy failed");
                }
            } catch (err) {
                alert(this.name + " szavazó linkjének másolása SIKERTELEN.\n\nHiba: " + err);
                this.$emit("error", err);
            }
        },
    },
};
</script>

<style scoped>
.copy-button {
    cursor: pointer;
}
</style>
