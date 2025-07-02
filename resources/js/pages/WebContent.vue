<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Web Content',
        href: '/web-content',
    },
];

interface WebContent {
    id: number;
    page: string;
    section: string;
    content: string;
}

const props = defineProps({ web_contents: { type: Array } });
const web_contents: WebContent[] = ref(props?.web_contents);
const mode: string = ref('view');

const edit_id = ref('');
const edit_content = ref('');

const edit = (id: number, content: string) => {
    mode.value = 'edit';
    edit_id.value = id;
    edit_content.value = content;
};

const submit = () => {
    const form = useForm({
        id: edit_id.value,
        content: edit_content.value,
    });

    form.post('/web-content', {
        preserveScroll: true,
        onSuccess: (data: any) => {
            web_contents.value = data.props.web_contents;
            edit_content.value = '';
            edit_id.value = '';
            mode.value = 'view';
        },
    });
};
</script>

<template>
    <Head title="Web Content" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="my-2 rounded-md border border-gray-50/50 p-4" v-for="content in web_contents" :key="content.id">
                <div class="flex justify-between">
                    <div>
                        <div>
                            <span class="font-extrabold"> Page: </span>
                            <span class="font-thin">
                                {{ content.page }}
                            </span>
                        </div>
                        <div>
                            <span class="font-extrabold"> Section: </span>
                            <span class="font-thin">
                                {{ content.section }}
                            </span>
                        </div>
                    </div>
                    <button
                        v-if="mode == 'view'"
                        @click="edit(content.id, content.content)"
                        class="h-fit w-fit rounded-lg bg-yellow-400 px-2 text-black"
                        type="button"
                    >
                        Edit
                    </button>
                </div>
                <div class="flex flex-col">
                    <span class="font-extrabold text-slate-500">Content:</span>
                    <span class="content" v-if="mode == 'view' || edit_id != content.id">
                        {{ content.content }}
                    </span>
                    <textarea
                        name=""
                        id=""
                        rows="5"
                        v-if="mode == 'edit' && edit_id == content.id"
                        class="w-full rounded-xl border-2 border-slate-200 bg-white text-black"
                        v-model="edit_content"
                    >
                    </textarea>
                </div>
            </div>
            <div class="flex gap-4">
                <button @click="submit" class="w-fit rounded-lg bg-yellow-400 px-2 text-black" v-if="mode == 'edit'" type="button">Submit</button>
                <button v-if="mode == 'edit'" @click="mode = 'view'" class="w-fit rounded-lg bg-red-700 px-2 text-white" type="button">Cancel</button>
            </div>
        </div>
    </AppLayout>
</template>
