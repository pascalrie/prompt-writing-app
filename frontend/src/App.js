import React from 'react';
import './App.css';
import NoteForm from './components/NoteForm';

const App = () => {
    return (
        <div className="container">
            <h1>Prompt Writing Project</h1>
            <NoteForm />
        </div>
    );
};

export default App;