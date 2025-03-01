import React, { useState } from "react";
import { useParams, useNavigate } from "react-router-dom";
import FetchCategoryDetails from "./FetchCategoryDetails";

const CategoryShowForm = () => {
    const { id } = useParams();
    const navigate = useNavigate();
    const [category, setCategory] = useState(null);
    const [expandedField, setExpandedField] = useState({});

    const toggleExpansion = (section, index) => {
        setExpandedField((prev) => ({
            ...prev,
            [`${section}-${index}`]: !prev[`${section}-${index}`],
        }));
    };

    const renderGrid = (data, section) => {
        if (Array.isArray(data) && data.length > 0) {
            return (
                <div style={styles.gridContainer}>
                    {data.map((item, index) => {
                        const isExpanded = expandedField[`${section}-${index}`];
                        return (
                            <div key={`${section}-${index}`} style={styles.card}>
                                <h2 style={styles.itemTitle}>{`${section.slice(0, -1)} ${index + 1}`}</h2>
                                <div style={styles.itemFields}>
                                    {Object.entries(item).map(([key, value]) => (
                                        <div key={key} style={styles.itemField}>
                                            <span style={styles.fieldKey}>{key}: </span>
                                            <span style={styles.fieldValue}>
                                            {typeof value === "string" && value.length > 50
                                                ? `${value.slice(0, 50)}...`
                                                : JSON.stringify(value)}
                                        </span>
                                        </div>
                                    ))}
                                </div>
                                {isExpanded && (
                                    <div style={styles.expandedContent}>
                                        <pre>{JSON.stringify(item, null, 2)}</pre>
                                    </div>
                                )}
                                <button
                                    style={styles.toggleButton}
                                    onClick={() => toggleExpansion(section, index)}
                                >
                                    {isExpanded ? "Collapse" : "Expand"}
                                </button>
                            </div>
                        );
                    })}
                </div>
            );
        } else {
            return <p style={styles.emptyMessage}>{`No ${section} available.`}</p>;
        }
    };


    return (
        <div>
            <div style={styles.header}>
                <h1>Category Details</h1>
                <button style={styles.backButton} onClick={() => navigate(-1)}>
                    Go Back
                </button>
            </div>
            <FetchCategoryDetails onFetch={setCategory} />
            {category ? (
                <div style={styles.details}>
                    <h2 style={styles.categoryTitle}>{category.title}</h2>
                    <div style={styles.section}>
                        <h3>Prompts</h3>
                        {renderGrid(category.prompts, "prompts")}
                    </div>
                    <div style={styles.section}>
                        <h3>Notes</h3>
                        {renderGrid(category.notes, "notes")}
                    </div>
                </div>
            ) : (
                <p style={styles.loading}>Loading category details...</p>
            )}
        </div>
    );
};
const styles = {
    gridContainer: {
        display: "grid",
        gridTemplateColumns: "repeat(auto-fill, minmax(300px, 1fr))",
        gap: "20px",
    },
    card: {
        backgroundColor: "#fff",
        border: "1px solid #ddd",
        borderRadius: "10px",
        padding: "20px",
        boxShadow: "0 4px 8px rgba(0, 0, 0, 0.1)",
        display: "flex",
        flexDirection: "column",
        justifyContent: "space-between",
        transition: "transform 0.2s, box-shadow 0.2s",
    },
    cardHover: {
        transform: "translateY(-5px)",
        boxShadow: "0 6px 12px rgba(0, 0, 0, 0.15)",
    },
    itemTitle: {
        fontSize: "1.2rem",
        fontWeight: "bold",
        marginBottom: "18px",
        color: "#333",
    },
    itemFields: {
        marginBottom: "15px",
    },
    itemField: {
        display: "flex",
        alignItems: "center",
        padding: "6px 0",
        borderBottom: "1px solid #f2f2f2",
        fontSize: "0.9rem",
    },
    fieldKey: {
        fontWeight: "bold",
        color: "#555",
        marginRight: "8px",
    },
    fieldValue: {
        color: "#333",
        wordBreak: "break-word",
    },
    expandedContent: {
        backgroundColor: "#f9f9f9",
        border: "1px solid #ddd",
        borderRadius: "8px",
        padding: "10px",
        maxHeight: "200px",
        overflow: "auto",
        whiteSpace: "pre-wrap",
        fontSize: "0.85rem",
        color: "#444",
    },
    toggleButton: {
        padding: "8px 16px",
        backgroundColor: "#007bff",
        color: "#fff",
        border: "none",
        borderRadius: "6px",
        cursor: "pointer",
        fontSize: "0.9rem",
        textAlign: "center",
        fontWeight: "bold",
        marginTop: "10px",
    },
    emptyMessage: {
        color: "#777",
        fontSize: "1rem",
        textAlign: "center",
    },
};


export default CategoryShowForm;