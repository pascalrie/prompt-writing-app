import React, { useState, useEffect } from 'react';
import axios from 'axios';
import PromptComponent from './PromptComponent';

const NoteCreateHomeForm = () => {
    const [content, setContent] = useState('');
    const [tag, setTag] = useState('');
    const [categories, setCategories] = useState([]);
    const [selectedCategory, setSelectedCategory] = useState('');
    const [title, setTitle] = useState('');
    const [prompt, setPrompt] = useState(null);
    const [isPromptLoading, setIsPromptLoading] = useState(true);

    useEffect(() => {
        const fetchCategories = async () => {
            try {
                const response = await axios.get('http://localhost:8083/api/category/list');
                const categoriesArray = Object.values(response.data).filter(item => item?.id && item?.title);
                setCategories(categoriesArray);
            } catch (error) {
                console.error('Error fetching categories:', error);
            }
        };
        fetchCategories();
    }, []);

    const handleSubmit = async () => {
        if (content.trim() === '' || tag.trim() === '' || selectedCategory === '' || !prompt) {
            alert('Please fill out all fields.');
            return;
        }

        const data = {
            title,
            content,
            tags: [tag],
            category: selectedCategory,
            promptId: prompt.id,
        };

        try {
            const response = await axios.post('http://localhost:8083/api/note/create', data);
            console.log('Server response:', response.data);
            alert('Note created successfully!');
        } catch (error) {
            console.error('Error submitting:', error);
            alert('An error occurred while creating the note.');
        }
    };

    return (
        <div>
            <div className="prompt-container">
                <PromptComponent
                    prompt={prompt}
                    setPrompt={setPrompt}
                    setIsLoading={setIsPromptLoading}
                />
            </div>

            <input
                type="text"
                placeholder="Enter a title of your note"
                value={title}
                onChange={(e) => setTitle(e.target.value)}
            />

            <textarea
                placeholder="Write your note here..."
                value={content}
                onChange={(e) => setContent(e.target.value)}
                rows="5"
            ></textarea>

            <select
                value={selectedCategory}
                onChange={(e) => setSelectedCategory(e.target.value)}
            >
                <option value="" disabled>
                    Select a category
                </option>
                {categories.map((category) => (
                    <option key={category.id} value={category.id}>
                        {category.title}
                    </option>
                ))}
            </select>

            <input
                type="text"
                placeholder="Enter a tag"
                value={tag}
                onChange={(e) => setTag(e.target.value)}
            />

            <button type="button" onClick={handleSubmit}>
                Submit
            </button>
        </div>
    );
};

export default NoteCreateHomeForm;