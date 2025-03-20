import matplotlib.pyplot as plt

def generate_conservation_plot(conservation_scores):
    """
    使用Matplotlib生成蛋白质保守性分析图表。
    
    :param conservation_scores: 蛋白质保守性分数列表
    """
    plt.plot(conservation_scores)
    plt.title('Protein Conservation Plot')
    plt.xlabel('Position')
    plt.ylabel('Conservation Score')
    plt.grid(True)
    plt.show()

if __name__ == "__main__":
    # 示例：保守性分数
    conservation_scores = [0.9, 0.8, 0.7, 0.5, 0.3, 0.2, 0.8, 0.9]
    generate_conservation_plot(conservation_scores)
