import React, { useState, useEffect } from "react";
import axios from "axios";

const PromptCreateForm = () => {
    const [categories, setCategories] = useState([]);
    const [selectedCategory, setSelectedCategory] = useState(null);
    const [promptTitle, setPromptTitle] = useState("");
    const [isSubmitting, setIsSubmitting] = useState(false);

    useEffect(() => {
        const fetchCategories = async () => {
            try {
                const response = await axios.get("http://localhost:8083/api/category/list");
                const data = response.data;

                if (Array.isArray(data)) {
                    setCategories(data);
                } else if (typeof data === "object") {
                    const categoryArray = Object.values(data).filter((cat) => typeof cat === "object");
                    setCategories(categoryArray);
                } else {
                    console.error("Unexpected data format for categories:", data);
                }
            } catch (error) {
                console.error("Error fetching categories:", error);
                setCategories([]);
            }
        };

        fetchCategories();
    }, []);

    const handleSubmit = async (e) => {
        e.preventDefault();

        if (!selectedCategory || !promptTitle) {
            alert("Both category and title are required.");
            return;
        }

        setIsSubmitting(true);

        try {
            const promptData = {
                category: selectedCategory.title,
                title: promptTitle,
            };

            await axios.post("http://localhost:8083/api/prompt/create", promptData);

            alert("Prompt created successfully!");
            setSelectedCategory(null);
            setPromptTitle("");
        } catch (error) {
            console.error("Error creating prompt:", error);
            alert("Failed to create prompt. Please try again.");
        } finally {
            setIsSubmitting(false);
        }
    };

    return (
        <form onSubmit={handleSubmit} style={styles.formContainer}>
            <h2>Create New Prompt</h2>
            <div style={styles.formGroup}>
                <label htmlFor="category" style={styles.label}>Category:</label>
                <select
                    id="category"
                    name="category"
                    value={selectedCategory ? selectedCategory.id : ""}
                    onChange={(e) => {
                        const selected = categories.find((cat) => cat.id === parseInt(e.target.value));
                        setSelectedCategory(selected || null);
                    }}
                    style={styles.select}
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
            </div>
            <div style={styles.formGroup}>
                <label htmlFor="title" style={styles.label}>Title:</label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    value={promptTitle}
                    onChange={(e) => setPromptTitle(e.target.value)}
                    placeholder="Enter a title"
                    style={styles.input}
                />
            </div>
            <button
                type="submit"
                disabled={isSubmitting}
                style={{
                    ...styles.button,
                    backgroundColor: isSubmitting ? "#ccc" : "#007bff",
                }}
            >
                {isSubmitting ? "Creating..." : "Create Prompt"}
            </button>
        </form>
    );
};

const styles = {
    formContainer: {
        maxWidth: "500px",
        margin: "0 auto",
        padding: "20px",
        border: "1px solid #ddd",
        borderRadius: "5px",
        backgroundColor: "#f9f9f9",
    },
    formGroup: {
        marginBottom: "15px",
    },
    label: {
        display: "block",
        fontWeight: "bold",
        marginBottom: "5px",
    },
    select: {
        width: "100%",
        padding: "8px",
        borderRadius: "4px",
        border: "1px solid #ccc",
        fontSize: "1rem",
    },
    input: {
        width: "100%",
        padding: "8px",
        borderRadius: "4px",
        border: "1px solid #ccc",
        fontSize: "1rem",
    },
    button: {
        width: "100%",
        padding: "10px",
        borderRadius: "4px",
        border: "none",
        color: "#fff",
        fontSize: "1rem",
        cursor: "pointer",
    },
};

export default PromptCreateForm;