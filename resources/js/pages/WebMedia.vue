<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Web Media',
        href: '/web-media',
    },
];

// interface WebMedia {
//     id: number
//     page: string,
//     section: string,
//     link: string
//     type: string
// }

const props = defineProps({web_media: {type: Object}})
const web_media = ref(props?.web_media)

const edit_id = ref('')
const edit_content = ref('')
const add_content_section = ref('')
const add_content = ref('')

const edit = (id: number) => {
    edit_id.value = id
}

const add = (section: string) => {
    add_content_section.value = section
}

const submit = () => {
    const form = useForm({
        id: edit_id.value,
        content: edit_content.value
    })

    form.post('/web-media/update', {
        preserveScroll: true,
        onSuccess: (data: any) => {
            web_media.value = data.props.web_media
            edit_content.value = ''
            edit_id.value = ''
        }
    })
}

const submitNew = () => {
    const form = useForm({
        content: add_content.value,
        content_section: add_content_section.value
    })

    form.post('/web-media/add', {
        preserveScroll: true,
        onSuccess: (data: any) => {
            web_media.value = data.props.web_media
            add_content_section.value = ''
            add_content.value = ''
        }
    })
}

</script>

<template>
    <Head title="Web Media" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div v-for="(section, key) in web_media" :key="section">
                <div class="flex justify-between">
                    <p class="font-bold text-xl">{{ key }}</p>
                    <Dialog>
                        <DialogTrigger as-child>
                            <button variant="destructive" @click="add(key)" class="bg-green-500 text-black w-fit h-fit my-auto rounded-md px-3 mb-2 hover:bg-slate-200 hover:cursor-pointer transition duration-200">Add</button>
                        </DialogTrigger>
                        <DialogContent>
                            <form class="space-y-6" @submit="submitNew">
                                <DialogHeader class="space-y-3">
                                    <DialogTitle>Add Image Link</DialogTitle>
                                    <Input type="url" placeholder="Enter Image Link" v-model="add_content" />
                                </DialogHeader>

                                <DialogFooter class="gap-2">
                                    <DialogClose as-child>
                                        <Button variant="secondary"> Cancel </Button>
                                    </DialogClose>

                                    <Button variant="secondary">
                                        <button type="submit">Add</button>
                                    </Button>
                                </DialogFooter>
                            </form>
                        </DialogContent>
                    </Dialog>
                    <!-- <button class="bg-green-500 text-black w-fit h-fit my-auto rounded-md px-3 mb-2 hover:bg-slate-200 hover:cursor-pointer transition duration-200">Add</button> -->
                </div>
                <div class="grid gap-4 md:grid-cols-3">
                    <div v-for="data in section" :key="data.id" class="relative">
                        <img v-if="data.type == 'image'" :src="data.link" alt="" class="rounded-lg hover:cursor-pointer">
                        <Dialog v-if="data.type == 'image'">
                            <DialogTrigger as-child>
                                <button variant="destructive" @click="edit(data.id)" class="bg-yellow-500 text-black w-fit h-fit absolute rounded-md px-3 -mt-8 ml-2 hover:bg-slate-800 hover:text-white hover:cursor-pointer transition duration-200">Edit</button>
                            </DialogTrigger>
                            <DialogContent>
                                <form class="space-y-6" @submit="submit">
                                    <DialogHeader class="space-y-3">
                                        <DialogTitle>Update Image Link</DialogTitle>
                                        <Input type="url" placeholder="Enter Image Link" v-model="edit_content" />
                                    </DialogHeader>

                                    <DialogFooter class="gap-2">
                                        <DialogClose as-child>
                                            <Button variant="secondary"> Cancel </Button>
                                        </DialogClose>

                                        <Button variant="secondary">
                                            <button type="submit">Update</button>
                                        </Button>
                                    </DialogFooter>
                                </form>
                            </DialogContent>
                        </Dialog>
                        <!-- <button v-if="data.type == 'image'" class="bg-yellow-500 text-black w-fit h-fit absolute rounded-md px-3 -mt-8 ml-2 hover:bg-slate-800 hover:text-white hover:cursor-pointer transition duration-200">Edit</button> -->
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
