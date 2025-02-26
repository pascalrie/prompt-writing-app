import React, {useState, useEffect} from 'react';
import NoteCreateForm from "./NoteCreateForm";

const NoteCreateHomeForm = () => {
    return (
        <div>
            <div className="note-create-form-container">
                <NoteCreateForm/>
            </div>
        </div>
    );
};

export default NoteCreateHomeForm;