import axios from "axios";
import {useState, useEffect} from "react";
import "../styles/CategoryGrid.css";
import {useNavigate} from "react-router-dom";

const FetchCategories = ({onFetch}) => {
    useEffect(() => {
        const fetchCategories = async () => {
            try {
                const response = await axios.get("http://localhost:8083/api/category/list");
                const categoriesArray = Object.values(response.data).filter(
                    (item) => item?.id && item?.title && item?.prompts
                );
                onFetch(categoriesArray);
            } catch (error) {
                console.error("Error fetching categories:", error);
            }
        };
        fetchCategories();
    }, [onFetch]);

    return null;
};

const CategoryListForm = () => {
    const navigate = useNavigate();
    const [categories, setCategories] = useState([]);
    const [expandedCategories, setExpandedCategories] = useState({});

    const toggleExpand = (id) => {
        setExpandedCategories((prev) => ({
            ...prev,
            [id]: !prev[id],
        }));
    };

    const renderPrompts = (prompts) => {
        if (Array.isArray(prompts) && prompts.length > 0) {
            return (
                <ul>
                    {prompts.map((prompt, index) => (
                        <li key={index}>{typeof prompt === "object" ? JSON.stringify(prompt) : prompt}</li>
                    ))}
                </ul>
            );
        }

        if (typeof prompts === "object" && prompts !== null && Object.keys(prompts).length > 0) {
            return (
                <ul>
                    {Object.entries(prompts).map(([key, value], index) => (
                        <li key={index}>
                            <strong>{key}:</strong>{" "}
                            {typeof value === "object" ? JSON.stringify(value) : value}
                        </li>
                    ))}
                </ul>
            );
        }

        return <p>No prompts available.</p>;
    };

    return (
        <div>
            <div className="header">
                <h1>Categories</h1>
            </div>
            <button className="create-button" onClick={() => navigate("/create/category")}>
                Create new
            </button>
            <FetchCategories onFetch={setCategories}/>
            {categories.length > 0 ? (
                <div className="category-grid">
                    {categories.map((category) => (
                        <div key={category.id} className="category-item">
                            <div className="category-header">
                                <span>{category.title}</span>
                                <button
                                    className="expand-button"
                                    onClick={() => navigate(`/show/category/${category.id}`)}
                                >Show
                                </button>
                            </div>
                            {expandedCategories[category.id] && (
                                <div className="category-prompts">{renderPrompts(category.prompts)}</div>
                            )}
                        </div>
                    ))}
                </div>
            ) : (
                <p>Loading categories...</p>
            )}
        </div>
    );
};

export default CategoryListForm;