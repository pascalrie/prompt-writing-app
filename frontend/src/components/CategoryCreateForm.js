import React, {useState} from 'react';
import axios from 'axios';

function CategoryCreateForm() {
    const [title, setTitle] = useState('');

    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            await axios.post('http://localhost:8083/api/category/create', {title});
            setTitle('');
        } catch (error) {
            console.error(error);
        }
    };

    return (
        <form onSubmit={handleSubmit}>
            <input
                type="text"
                value={title}
                onChange={(e) => setTitle(e.target.value)}
                placeholder="Category Title"
                required
            />
            <button type="submit">Create Category</button>
        </form>
    );
}

export default CategoryCreateForm;