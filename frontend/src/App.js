import React from 'react';
import './styles/App.css';
import MenuBar from "./components/MenuBar";
import {BrowserRouter as Router, Routes, Route} from "react-router-dom";
import HomePage from "./pages/HomePage";
import AboutPage from "./pages/AboutPage";
import NotesPage from "./pages/NotesPage";
import PromptsPage from "./pages/PromptsPage";
import CategoriesPage from "./pages/CategoriesPage";
import ContactPage from "./pages/ContactPage";
import FolderPage from "./pages/FolderPage";
import NoteCreateFormWithoutPrompts from "./components/NoteCreateFormWithoutPrompts";
import PromptCreateForm from "./components/PromptCreateForm";


const App = () => {
    return (
        <div className="container">
            <Router>
                <MenuBar/>
                <Routes>
                    <Route path="/folder" element={<FolderPage/>}/>
                    <Route path="/create/note" element={<NoteCreateFormWithoutPrompts/>}/>
                    <Route path="/create/prompt" element={<PromptCreateForm/>}/>
                    <Route path="/notes" element={<NotesPage/>}/>
                    <Route path="/prompts" element={<PromptsPage/>}/>
                    <Route path="/categories" element={<CategoriesPage/>}/>
                    <Route path="/about" element={<AboutPage/>}/>
                    <Route path="/contact" element={<ContactPage/>}/>
                    <Route path="/home" element={<HomePage/>}/>
                    <Route path="/" element={<HomePage/>}/>
                </Routes>
            </Router>
        </div>
    );
};

export default App;