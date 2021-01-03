<template>
    <div>
        <form method="post" @submit.prevent="sendSourceUri">
            <label for="media_audio_source_id">Source id</label>
            <input type="text" name="media_audio_source_id" id="media_audio_source_id" v-model="form.media_audio_source_id"/>
            <button type="submit">Send</button>
        </form>
    </div>
</template>

<script>

export default {
    data() {
        return {
            form: this.$inertia.form(
                {
                    '_method': 'POST',
                    media_audio_source_id: this.media_audio_source_id,
                },
                {
                    bag: "sendSourceUri",
                    resetOnSuccess: true,
                }
            ),
        };
    },

    methods: {
        sendSourceUri() {
            this.form.post('/media-audio', {
                preserveScroll: true
            });
        },
    },
};
</script>

<style></style>
