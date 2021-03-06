<template>
<div class="import-contacts">
    <div class="file-uploader flex justify-center flex-col" v-if="tempCode === null && success === false">
        <vue-upload-component class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
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
        <button @click="$refs.upload.active = true" v-if="files.length > 0" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-5">{{upload_text}}</button>
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
                    <select @change="form[key] = parseInt($event.target.value)" :key="key" required  class="shadow appearance-none border border-red-500 rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline">
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
            let fieldName = field.toLowerCase().replace(/\s/g, "_");
            // Do a check to see if there is an exact header match,if not
            // check to see if a name is similar
            let headerMatch = this.headers.find(header => {
                return header == field;
            });
            if(headerMatch && headerMatch.length == 1){
                this.form[fieldName] = parseInt(Object.keys(this.headers).find(key => this.headers[key] === headerMatch));
                return headerMatch;
            }

            headerMatch = this.headers.find(header => {
                return header.toLowerCase().includes(field.toLowerCase());
            });
            if(headerMatch && headerMatch.length > 0){
                headerMatch = Array.isArray(headerMatch) ? headerMatch[0] : headerMatch;
                this.form[fieldName] = parseInt(Object.keys(this.headers).find(key => this.headers[key] === headerMatch));
                return headerMatch;
            }
            this.form[fieldName] = null;
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
