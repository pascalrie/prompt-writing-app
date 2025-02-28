import React, { useState, useEffect } from 'react';
import axios from 'axios';
import getRandomPrompt from "../fetchPrompt";

const NoteCreateFormWithoutPrompts = () => {
    const [content, setContent] = useState('');
    const [tag, setTag] = useState('');
    const [categories, setCategories] = useState([]);
    const [selectedCategory, setSelectedCategory] = useState('');
    const [prompts, setPrompts] = useState([]);
    const [selectedPrompt, setSelectedPrompt] = useState('');
    const [title, setTitle] = useState('');
    const [prompt, setPrompt] = useState(null);
    const [isLoading, setIsLoading] = useState(true);

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

        const fetchPrompt = async () => {
            setIsLoading(true);
            try {
                const data = await getRandomPrompt();
                setPrompt(data);
            } catch (error) {
                console.error('Error fetching random prompt:', error);
            } finally {
                setIsLoading(false);
            }
        };

        const fetchPrompts = async () => {
            try {
                const response = await axios.get('http://localhost:8083/api/prompt/list');
                const promptsArray = Object.values(response.data)
                    .filter(item => item?.id && item?.title)
                    .sort((a, b) => a.id - b.id);
                setPrompts(promptsArray);
            } catch (error) {
                console.error('Error fetching prompts:', error);
            }
        };

        fetchCategories();
        fetchPrompt();
        fetchPrompts();
    }, [setCategories, setPrompt, setIsLoading]);

    const handleSubmit = async () => {
        if (content.trim() === '' || tag.trim() === '' || selectedCategory === '' || selectedPrompt === '') {
            alert('Please fill out all fields.');
            return;
        }

        const data = {
            title,
            content,
            tags: [tag],
            category: selectedCategory,
            promptId: selectedPrompt,
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

            <select
                value={selectedPrompt}
                onChange={(e) => setSelectedPrompt(e.target.value)}
            >
                <option value="" disabled>
                    Select a prompt
                </option>
                {prompts.map((prompt) => (
                    <option key={prompt.id} value={prompt.id}>
                        {prompt.title}
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

export default NoteCreateFormWithoutPrompts;