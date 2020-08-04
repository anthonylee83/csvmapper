<template>
<div class="import-contacts">
    <div class="file-uploader flex justify-center" v-if="tempCode === null && success === false">
        <vue-upload-component class="bg-green-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
            :post-action="post_url"
            :extensions="extensions"
            :accept="mime_types"
            v-model="files"
            ref="upload"
            @input-file="inputFile"
            :headers="{'X-CSRF-TOKEN': csrf_token}">
            <i class="fa fa-plus"></i>
            {{import_csv_text}}
        </vue-upload-component>
        <button v-if="files.length > 0" class="btn btn-blue" @click.prevent="$refs.upload.active = true">{{upload_text}}</button>
    </div>
    <div v-else-if="success === false">
        <form @submit.prevent="submit">
        <table class="table-auto">
            <thead>
                <tr>
                    <th class="px-4 py-2">Contact Field</th>
                    <th class="px-4 py-2">Import Field</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(field, key) in contact_fields" :key="field">
                <td class="border px-4 py-2">{{field}}</td>
                <td class="border px-4 py-2">
                    <select v-model="form[key]" :key="key" required>
                        <option>Select Field</option>
                        <option :value="key" v-for="(header, key) in headers" :key="header" :selected="header === matchHeader(field)">{{header}}</option>
                    </select>
                    </td>
                </tr>
        </tbody>
        </table>
        <button type="submit" class="bg-green-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Import</button>
        </form>
    </div>
    <div v-else>
        <h3>Import Success</h3>
        <p>Your contacts were imported successfully</p>
    </div>
</div>
</template>

<script>
import VueUploadComponent from 'vue-upload-component'
export default {
    name: "FileUpload",
    components: {
        VueUploadComponent
    },
    props: {
        import_csv_text: {
            type: String,
            default: ''
        },
        upload_text: {
            type: String,
            default: ''
        },
        post_url: {
            type: String,
            default: '/contacts/import/prepare'
        },
        extensions: {
            type: String,
            default: 'csv'
        },
        mime_types: {
            type: String,
            default: 'text/csv'
        },
        contact_fields: {
            type: Object,
            defualt: {}
        }
    },
    data: () => ({
        files: [],
        tempCode: null,
        headers: [],
        form: {},
        success: false,
    }),
    computed: {
        csrf_token() {
            return window.axios.defaults.headers.common['X-CSRF-TOKEN'];
        }
    },
    methods:{
        inputFile(newFile, oldFile, prevent) {
            //If htis is a response from our upload, lets load the headers and tempCode
            if(newFile && oldFile && newFile.response != oldFile.response && newFile.xhr.status === 200){
                this.tempCode = newFile.response.tempCode
                this.headers = newFile.response.headers
            }

        },
        matchHeader(field)
        {
            // Do a check to see if there is an exact header match,if not
            // check to see if a name is similar
            let headerMatch = this.headers.find(header => {
                return header == field;
            });
            if(headerMatch && headerMatch.length == 1)
                return headerMatch;

            headerMatch = this.headers.find(header => {
                return header.toLowerCase().includes(field.toLowerCase());
            });
            if(headerMatch && headerMatch.length > 0)
                return Array.isArray(headerMatch) ? headerMatch[0] : headerMatch;

            return false;
        },
        submit(evt)
        {
            let data = {
                form: this.form,
                tempCode: this.tempCode,
                csvHeaders: this.headers
            }
            axios.post('/contacts/import', data)
            .then(response => {

                //Just putting a general success message, would probably use vue router in real app.
                this.success = true;
            })
            .catch(err=> {
                //We would display an error message or take to another screen to correct issues
            })
        }
    }

}
</script>
