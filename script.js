document.getElementById("learn-more").addEventListener("click", function() {
    alert("This platform offers various bioinformatics analysis tools. Explore now!");
});

// 处理搜索表单的提交
document.getElementById("search-form").addEventListener("submit", function(event) {
    event.preventDefault(); // 防止表单刷新页面

    const proteinFamily = document.getElementById("protein-family").value;
    const taxonomy = document.getElementById("taxonomy").value;

    // 在控制台输出输入的蛋白质家族和分类群信息
    console.log("Protein Family:", proteinFamily);
    console.log("Taxonomy:", taxonomy);

    // 重定向到查询结果页面
    window.location.href = "results.html";
});
