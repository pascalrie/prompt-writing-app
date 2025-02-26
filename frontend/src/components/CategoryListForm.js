import axios from "axios";
import { useEffect, useState } from "react";

const FetchCategories = ({ onFetch }) => {
    useEffect(() => {
        const fetchCategories = async () => {
            try {
                const response = await axios.get('http://localhost:8083/api/category/list');
                const categoriesArray = Object.values(response.data).filter(item => item?.id && item?.title);
                onFetch(categoriesArray);
            } catch (error) {
                console.error('Error fetching categories:', error);
            }
        };
        fetchCategories();
    }, [onFetch]);

    return null;
};

const CategoryListForm = () => {
    const [categories, setCategories] = useState([]);

    return (
        <div>
            <h1>Categories</h1>
            <FetchCategories onFetch={setCategories} />
            <ul>
                {categories.length > 0 ? (
                    categories.map((category) => (
                        <li key={category.id}>{category.title}</li>
                    ))
                ) : (
                    <p>Loading categories...</p>
                )}
            </ul>
        </div>
    );
};

export default CategoryListForm;