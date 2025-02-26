import React from 'react';
import './App.css';
import NoteCreateHomeForm from './components/NoteCreateHomeForm';
import MenuBar from "./components/MenuBar";
import {BrowserRouter as Router, Routes, Route} from "react-router-dom";
import HomePage from "./pages/HomePage";
import AboutPage from "./pages/AboutPage";
import NotesPage from "./pages/NotesPage";
import PromptsPage from "./pages/PromptsPage";
import CategoriesPage from "./pages/CategoriesPage";
import ContactPage from "./pages/ContactPage";


const App = () => {
    return (
        <div className="container">
            <Router>
                <MenuBar/>
                <Routes>
                    <Route path="/notes" element={<NotesPage/>}/>
                    <Route path="/prompts" element={<PromptsPage/>}/>
                    <Route path="/categories" element={<CategoriesPage/>}/>
                    <Route path="/about" element={<AboutPage/>}/>
                    <Route path="/contact" element={<ContactPage/>}/>
                    <Route path="/home" element={<NoteCreateHomeForm/>}/>
                    <Route path="/" element={<HomePage/>}/>
                </Routes>
            </Router>
        </div>
    );
};

export default App;